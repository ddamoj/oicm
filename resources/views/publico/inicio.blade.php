<x-layouts.publico>
    <x-hero
        :alto="true"
        titulo="Transparencia y control interno al alcance de todos"
        subtitulo="El Órgano Interno de Control Municipal de Oaxaca de Juárez concentra en un solo espacio la normatividad, los documentos, avisos y trámites institucionales."
    >
        <div class="flex flex-col gap-3 sm:flex-row">
            <x-boton href="{{ route('documentos') }}" variante="acento">Consultar documentos</x-boton>
            <x-boton href="{{ route('normatividad') }}" variante="secundario" class="!border-white !text-white hover:!bg-white/10">
                Ver normatividad
            </x-boton>
        </div>
    </x-hero>

    <x-seccion
        antetitulo="Servicio al ciudadano"
        titulo="Acceso rápido"
        descripcion="Los trámites y consultas que con más frecuencia solicitan las personas servidoras públicas y la ciudadanía."
    >
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <x-tarjeta href="{{ route('documentos') }}" :flotante="true" acento="primario">
                <x-slot:icono>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75h6m-6 3h4.5m-1.5-16.5H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0019.5 19.5V11.25m-7.5-9.75l7.5 7.5m-7.5-7.5v6a1.5 1.5 0 001.5 1.5h6" />
                    </svg>
                </x-slot:icono>
                <x-badge variante="primario" :punto="true">Formatos y oficios</x-badge>
                <h3 class="mt-4 text-lg font-bold tracking-tight">Documentos institucionales</h3>
                <p class="mt-2 text-sm text-texto-secundario">Formatos, oficios y bases de datos, organizados por categoría y disponibles sin necesidad de iniciar sesión.</p>
            </x-tarjeta>

            <x-tarjeta href="{{ route('normatividad') }}" :flotante="true" acento="acento">
                <x-slot:icono>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18L5.25 6.75m6.75-3.75L18.75 6.75M3 10.5l2.25-3.75L7.5 10.5m-4.5 0h4.5m-4.5 0a2.25 2.25 0 002.25 2.25A2.25 2.25 0 007.5 10.5M16.5 10.5l2.25-3.75 2.25 3.75m-4.5 0h4.5m-4.5 0a2.25 2.25 0 002.25 2.25 2.25 2.25 0 002.25-2.25M4.5 20.25h15" />
                    </svg>
                </x-slot:icono>
                <x-badge variante="acento" :punto="true">Marco legal</x-badge>
                <h3 class="mt-4 text-lg font-bold tracking-tight">Normatividad aplicable</h3>
                <p class="mt-2 text-sm text-texto-secundario">Leyes, reglamentos y lineamientos de ámbito federal, estatal y municipal que rigen la actuación del OICM.</p>
            </x-tarjeta>

            <x-tarjeta href="{{ route('enlaces') }}" :flotante="true" acento="neutro">
                <x-slot:icono>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5l6-6m0 0h-4.5m4.5 0v4.5M11.25 6H6.75A2.25 2.25 0 004.5 8.25v9A2.25 2.25 0 006.75 19.5h9a2.25 2.25 0 002.25-2.25v-4.5" />
                    </svg>
                </x-slot:icono>
                <x-badge :punto="true">Trámites externos</x-badge>
                <h3 class="mt-4 text-lg font-bold tracking-tight">Enlaces de interés</h3>
                <p class="mt-2 text-sm text-texto-secundario">Declaración patrimonial, evaluación de control interno, entrega-recepción y otros trámites vinculados.</p>
            </x-tarjeta>
        </div>
    </x-seccion>

    <x-seccion antetitulo="Comunicación institucional" titulo="Últimas noticias" descripcion="Comunicados y avisos publicados por el OICM." :alterna="true">
        <x-vacio
            titulo="Aún no hay noticias publicadas"
            descripcion="En cuanto el OICM publique un comunicado, aparecerá aquí de forma automática."
        />
    </x-seccion>
</x-layouts.publico>
