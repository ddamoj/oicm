<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\BitacoraAuditoria;
use App\Models\Rol;
use App\Models\User;
use App\Support\ReglasContrasena;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de usuarios administrativos (RF-USR-001). Se monta una sola
 * vez dentro del modal de la lista de usuarios y cambia de modo (nuevo/editar)
 * por eventos Livewire, en vez de una ruta propia por acción.
 */
class FormularioUsuario extends Component
{
    public ?int $usuarioId = null;

    public string $name = '';

    public string $email = '';

    public string $rolId = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected function reglas(): array
    {
        $reglas = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->usuarioId)],
            'rolId' => ['required', Rule::exists('roles', 'id')],
        ];

        // La contraseña es obligatoria al crear y opcional al editar (solo si se desea cambiar).
        if ($this->usuarioId === null || $this->password !== '') {
            $reglas['password'] = array_merge(['required', 'string'], ReglasContrasena::reglas(), ['confirmed']);
        }

        return $reglas;
    }

    protected array $mensajes = [
        'name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo es obligatorio.',
        'email.email' => 'Escribe un correo válido.',
        'email.unique' => 'Ya existe un usuario registrado con ese correo.',
        'rolId.required' => 'Selecciona un rol.',
        'rolId.exists' => 'El rol seleccionado no es válido.',
        'password.confirmed' => 'La confirmación de contraseña no coincide.',
    ];

    #[On('usuario:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-usuario');
    }

    #[On('usuario:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $usuario = User::query()->findOrFail($id);

            $this->usuarioId = $usuario->id;
            $this->name = $usuario->name;
            $this->email = $usuario->email;
            $this->rolId = (string) $usuario->rol_id;

            $this->dispatch('abrir-modal', nombre: 'formulario-usuario');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el usuario a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el usuario seleccionado.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['usuarioId', 'name', 'email', 'rolId', 'password', 'password_confirmation']);
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-usuarios')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes);

        try {
            $esAlta = $this->usuarioId === null;

            if ($esAlta) {
                $usuario = User::query()->create([
                    'name' => $datosValidados['name'],
                    'email' => $datosValidados['email'],
                    'rol_id' => (int) $datosValidados['rolId'],
                    'password' => Hash::make($datosValidados['password']),
                    'email_verified_at' => now(),
                    'activo' => true,
                ]);

                BitacoraAuditoria::registrar('usuario_creado', 'User', $usuario->id, [
                    'nombre' => $usuario->name,
                    'correo' => $usuario->email,
                    'rol' => $usuario->rol?->clave,
                ]);
            } else {
                $usuario = User::query()->findOrFail($this->usuarioId);
                $rolAnterior = $usuario->rol?->clave;

                if ($usuario->activo && $rolAnterior === 'administrador' && (int) $datosValidados['rolId'] !== $usuario->rol_id
                    && $this->esUltimoAdministradorActivo($usuario)) {
                    $this->dispatch('mostrar-error', mensaje: 'No puedes cambiar este rol: es el único administrador activo del sistema.');

                    return;
                }

                $usuario->fill([
                    'name' => $datosValidados['name'],
                    'email' => $datosValidados['email'],
                    'rol_id' => (int) $datosValidados['rolId'],
                ]);

                if ($this->password !== '') {
                    $usuario->password = Hash::make($datosValidados['password']);
                }

                $usuario->save();

                BitacoraAuditoria::registrar('usuario_actualizado', 'User', $usuario->id, [
                    'nombre' => $usuario->name,
                    'correo' => $usuario->email,
                ]);

                if ($rolAnterior !== $usuario->rol?->clave) {
                    BitacoraAuditoria::registrar('rol_cambiado', 'User', $usuario->id, [
                        'rol_anterior' => $rolAnterior,
                        'rol_nuevo' => $usuario->rol?->clave,
                    ]);

                    // Un cambio de rol revoca privilegios de inmediato: se cierran sus sesiones
                    // activas para que el nuevo rol (o la pérdida de acceso) surta efecto ya.
                    DB::table('sessions')->where('user_id', $usuario->id)->delete();
                }
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-usuario');
            $this->dispatch('usuario-guardado');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Usuario creado correctamente.' : 'Usuario actualizado correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar el usuario.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar el usuario. Intenta de nuevo.');
        }
    }

    /**
     * True si $usuario es el único administrador activo del sistema —
     * protege contra dejarlo sin acceso administrativo al cambiarle el rol.
     */
    private function esUltimoAdministradorActivo(User $usuario): bool
    {
        return User::query()
            ->where('activo', true)
            ->whereHas('rol', fn ($consulta) => $consulta->where('clave', 'administrador'))
            ->where('id', '!=', $usuario->id)
            ->doesntExist();
    }

    #[Computed]
    public function roles(): Collection
    {
        return Rol::query()->orderBy('nombre')->get();
    }

    public function render()
    {
        return view('livewire.admin.usuarios.formulario-usuario');
    }
}
