<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\BitacoraAuditoria;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y acciones de administración de usuarios (RF-USR-001). Autorizado
 * exclusivamente al rol "administrador" desde la ruta (middleware `rol`).
 */
class ListaUsuarios extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroRol = '';

    public string $filtroEstado = '';

    protected $queryString = ['busqueda', 'filtroRol', 'filtroEstado'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroRol', 'filtroEstado'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('usuario:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('usuario:editar', id: $id);
    }

    /**
     * Desactiva un usuario (reversible): revoca su acceso de inmediato
     * cerrando sus sesiones activas, sin perder el registro ni su historial.
     */
    public function desactivar(int $id): void
    {
        $this->cambiarEstado($id, activo: false);
    }

    public function activar(int $id): void
    {
        $this->cambiarEstado($id, activo: true);
    }

    private function cambiarEstado(int $id, bool $activo): void
    {
        try {
            $usuario = User::query()->findOrFail($id);

            $this->authorize($activo ? 'activar' : 'desactivar', $usuario);

            $usuario->update(['activo' => $activo]);

            if (! $activo) {
                // Revocación inmediata: se eliminan sus sesiones abiertas en el acto.
                DB::table('sessions')->where('user_id', $usuario->id)->delete();
            }

            BitacoraAuditoria::registrar($activo ? 'usuario_activado' : 'usuario_desactivado', 'User', $usuario->id, [
                'nombre' => $usuario->name,
                'correo' => $usuario->email,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $activo ? 'Usuario reactivado.' : 'Usuario desactivado. Su acceso fue revocado de inmediato.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar el estado del usuario.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar el usuario.');
        }
    }

    /**
     * Baja definitiva (softDelete): conserva el historial y la bitácora,
     * pero retira al usuario de toda operación futura y revoca su acceso.
     */
    public function eliminar(int $id): void
    {
        try {
            $usuario = User::query()->findOrFail($id);

            $this->authorize('delete', $usuario);

            DB::table('sessions')->where('user_id', $usuario->id)->delete();

            BitacoraAuditoria::registrar('usuario_eliminado', 'User', $usuario->id, [
                'nombre' => $usuario->name,
                'correo' => $usuario->email,
            ]);

            $usuario->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Usuario eliminado correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar el usuario.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el usuario.');
        }
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('usuario-guardado')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', User::class);

        $usuarios = User::query()
            ->with('rol')
            ->when($this->busqueda !== '', function ($consulta) {
                $consulta->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->busqueda}%")
                        ->orWhere('email', 'like', "%{$this->busqueda}%");
                });
            })
            ->when($this->filtroRol !== '', fn ($consulta) => $consulta->where('rol_id', $this->filtroRol))
            ->when($this->filtroEstado !== '', fn ($consulta) => $consulta->where('activo', $this->filtroEstado === 'activo'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.usuarios.lista-usuarios', [
            'usuarios' => $usuarios,
            'roles' => Rol::query()->orderBy('nombre')->get(),
        ]);
    }
}
