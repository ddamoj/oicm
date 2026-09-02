# AGENTS.md

Este proyecto usa `CLAUDE.md` como archivo principal de guía para agentes de IA (incluye las guías generadas por Laravel Boost tras `php artisan boost:install`, más el contexto y las convenciones del proyecto). Léelo primero.

## Convenciones obligatorias del proyecto (resumen)

Definidas por el cliente en `context/promptinicial.md` y detalladas en `context/plandetrabajo.md` §5:

1. `try/catch` en toda lógica falible; nunca dejar excepciones sin manejar frente al usuario.
2. Null-safety en todo acceso a propiedades y relaciones (`?->`, `??`, validación previa).
3. Comentar cada bloque desarrollado explicando qué hace — el cliente lee el código.
4. SweetAlert2 para todo mensaje al usuario; nunca alertas Blade ni cadenas flash sueltas.
5. Pruebas después de cada funcionalidad o fase, y actualización del checklist en `context/plandetrabajo.md`.
6. Código limpio: nombres descriptivos en español para el dominio, PSR-12 vía Pint.
7. SAST/DAST: validación, escapado, autorización y consultas no inyectables por defecto.
8. Vistas propias, nunca el scaffolding por defecto de Laravel o Jetstream.
9. Mobile-first y responsive en cada vista.
10. Todo en español: interfaz, comentarios, commits y documentación.

Ver `CLAUDE.md` para contexto completo del proyecto, comandos, stack y lenguaje de diseño.
