@props([
    'lineas' => 3,
])

{{-- Estado de carga: bloques con el shimmer institucional definido en app.css --}}
<div {{ $attributes->merge(['class' => 'animate-pulse space-y-3', 'role' => 'status', 'aria-label' => 'Cargando contenido']) }}>
    <div class="cargando-shimmer h-6 w-2/3 rounded-md"></div>

    @for ($i = 0; $i < $lineas; $i++)
        <div class="cargando-shimmer h-4 rounded-md {{ $i === $lineas - 1 ? 'w-[45%]' : 'w-full' }}"></div>
    @endfor
</div>
