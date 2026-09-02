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
        titulo="Acceso rápido"
        descripcion="Los trámites y consultas que con más frecuencia solicitan las personas servidoras públicas y la ciudadanía."
    >
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <x-tarjeta href="{{ route('documentos') }}" :flotante="true">
                <x-badge variante="primario">Formatos y oficios</x-badge>
                <h3 class="mt-4 text-lg font-semibold">Documentos institucionales</h3>
                <p class="mt-2 text-sm text-texto-secundario">Formatos, oficios y bases de datos, organizados por categoría y disponibles sin necesidad de iniciar sesión.</p>
            </x-tarjeta>

            <x-tarjeta href="{{ route('normatividad') }}" :flotante="true">
                <x-badge variante="acento">Marco legal</x-badge>
                <h3 class="mt-4 text-lg font-semibold">Normatividad aplicable</h3>
                <p class="mt-2 text-sm text-texto-secundario">Leyes, reglamentos y lineamientos de ámbito federal, estatal y municipal que rigen la actuación del OICM.</p>
            </x-tarjeta>

            <x-tarjeta href="{{ route('enlaces') }}" :flotante="true">
                <x-badge>Trámites externos</x-badge>
                <h3 class="mt-4 text-lg font-semibold">Enlaces de interés</h3>
                <p class="mt-2 text-sm text-texto-secundario">Declaración patrimonial, evaluación de control interno, entrega-recepción y otros trámites vinculados.</p>
            </x-tarjeta>
        </div>
    </x-seccion>

    <x-seccion titulo="Últimas noticias" descripcion="Comunicados y avisos publicados por el OICM." :alterna="true">
        <x-vacio
            titulo="Aún no hay noticias publicadas"
            descripcion="En cuanto el OICM publique un comunicado, aparecerá aquí de forma automática."
        />
    </x-seccion>
</x-layouts.publico>
