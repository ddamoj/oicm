<x-layouts.publico titulo="Aviso de privacidad">
    <x-hero
        titulo="Aviso de privacidad"
        subtitulo="Tratamiento de datos personales del micrositio del OICM y avisos oficiales del municipio por proceso."
    />

    <x-seccion ancho="max-w-3xl">
        <x-migas :items="['Aviso de privacidad' => null]" class="mb-8" />

        {{-- Tratamiento de datos del propio micrositio: verificado en código, no
             hay ningún formulario público que recolecte datos personales. --}}
        <div class="prose prose-lg max-w-none text-texto prose-headings:text-texto prose-a:text-primario">
            <p>
                El micrositio institucional del Órgano Interno de Control Municipal (OICM) del
                Municipio de Oaxaca de Juárez es un canal de consulta pública: la ciudadanía y las
                personas servidoras públicas municipales pueden navegar la normatividad, los
                documentos, las noticias, los enlaces y la información institucional
                <strong>sin registrarse ni proporcionar datos personales</strong>.
            </p>

            <h2>Datos que sí se tratan</h2>
            <ul>
                <li>
                    <strong>Cuentas de acceso administrativo:</strong> nombre, correo institucional y
                    contraseña de las personas administradoras y administradoras de contenido del
                    micrositio, con la finalidad exclusiva de operar el panel de administración.
                </li>
                <li>
                    <strong>Bitácora de auditoría:</strong> cada acción administrativa registra quién la
                    realizó, cuándo, la dirección IP y el navegador utilizado, con la finalidad de
                    rendición de cuentas y trazabilidad de la información publicada.
                </li>
                <li>
                    <strong>Cookie de sesión:</strong> técnica y estrictamente necesaria para mantener
                    la sesión de una persona administradora autenticada; no se emplea con fines de
                    rastreo ni publicidad.
                </li>
            </ul>

            <p>
                Este tratamiento se realiza con fundamento en la Ley de Protección de Datos
                Personales en Posesión de Sujetos Obligados del Estado de Oaxaca. Este apartado
                queda sujeto a validación por la Unidad de Transparencia del OICM antes de la
                puesta en marcha del micrositio.
            </p>
        </div>
    </x-seccion>

    <x-seccion antetitulo="Contraloría Interna Municipal" titulo="Avisos oficiales por proceso" descripcion="Publicados por el Municipio de Oaxaca de Juárez en su portal de transparencia. Se abren en una pestaña nueva." alterna>
        @if ($avisos->isEmpty())
            <x-vacio titulo="Avisos en preparación" descripcion="Esta información se está cargando. Vuelve pronto." />
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($avisos as $aviso)
                    <x-tarjeta
                        href="{{ $aviso->url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        flotante
                        acento="primario"
                    >
                        <h3 class="font-semibold text-texto">{{ $aviso->nombre }}</h3>

                        @if ($aviso->descripcion)
                            <p class="mt-2 text-sm text-texto-secundario">{{ $aviso->descripcion }}</p>
                        @endif

                        <p class="mt-3 text-xs font-medium uppercase tracking-wide text-primario">
                            transparencia.municipiodeoaxaca.gob.mx
                            <span class="sr-only">— se abre en una pestaña nueva</span>
                        </p>
                    </x-tarjeta>
                @endforeach
            </div>
        @endif
    </x-seccion>
</x-layouts.publico>
