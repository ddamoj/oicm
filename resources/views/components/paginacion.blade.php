@props(['paginador'])

{{-- Envoltorio delgado sobre el LengthAwarePaginator de Laravel con la vista institucional --}}
@if ($paginador)
    <div {{ $attributes->merge(['class' => 'mt-10']) }}>
        {{ $paginador->onEachSide(1)->links('vendor.pagination.oicm') }}
    </div>
@endif
