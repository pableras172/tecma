description: "Contexto global del proyecto Laravel, Filament y Tailwind para guiar al asistente en todo el repositorio."
Contexto del proyecto
Stack principal
Laravel 12 (PHP)
FilamentPHP para panel administrativo
TailwindCSS para estilos
Vite para assets
MySQL como base de datos
Objetivo
Priorizar soluciones idiomáticas de Laravel y Filament.
Mantener código claro, mantenible y consistente con convenciones del framework.
Reglas de implementación
Usar Form Requests para validación cuando la lógica lo amerite.
Evitar lógica pesada en controllers; mover a servicios cuando crezca la complejidad.
Usar Eloquent y relaciones correctamente; evitar N+1 con eager loading.
En cambios de esquema, crear migraciones seguras y reversibles.
Si una columna pasa a NOT NULL en rollback, preparar datos antes para evitar errores.
Para UI admin, preferir componentes de Filament antes que soluciones manuales.
Para estilos, usar utilidades Tailwind y evitar CSS ad-hoc innecesario.
Calidad
Cuando propongas cambios de negocio, incluir tests Feature o Unit recomendados.
Señalar riesgos de regresión y compatibilidad de datos.
Priorizar legibilidad sobre micro-optimizaciones prematuras.
Respuesta esperada del asistente
Explicar brevemente el porqué técnico de cambios no triviales.
Si hay varias opciones, recomendar una y justificar.
En refactors grandes, proponer pasos incrementales.