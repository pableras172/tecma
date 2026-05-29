applyTo: "app/Filament/**/*.php"
description: "Usar patrones de FilamentPHP para Resources, Forms, Tables, Actions, Filters y Pages."
Guía para archivos Filament
Estructura
Seguir estructura estándar Resource, Page y RelationManager.
Formularios
Usar labels claros en español del dominio.
Definir required solo cuando el campo realmente sea obligatorio en base de datos o negocio.
Configurar opciones de Select con claves estables.
Tablas
Evitar columnas duplicadas para el mismo dato salvo razón funcional.
Preferir columnas con formato legible como date, badge, icon y color.
Si hay tooltips o listas de relaciones, evitar consultas costosas por fila.
Acciones
Mantener acciones claras y consistentes.
Para bulk actions, validar impacto y permisos.
Validación y datos
Reflejar en UI las reglas reales del modelo y migración.
Si un campo pasó a nullable en base de datos, no marcarlo required en formulario.
Rendimiento
Revisar relaciones usadas en columnas para prevenir N+1.
Legibilidad
Eliminar imports no usados.
Evitar bloques comentados largos; dejar solo código vigente o justificación corta.