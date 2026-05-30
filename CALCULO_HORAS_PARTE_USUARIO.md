# Como se calculan las horas en un Parte (guia para usuarios)

Este documento explica, en lenguaje sencillo, como calcula la aplicacion las horas y los totales de un parte de trabajo.

## 1) Que pasa cuando guardas una linea

Cada vez que creas, editas o borras una linea del parte:

1. La aplicacion recalcula automaticamente las horas de esa linea.
2. Actualiza el resumen general del parte.
3. Recalcula tambien el resumen por categoria profesional de los tecnicos.

No hace falta recalcular nada manualmente.

## 2) Horario normal y horario personalizado

La aplicacion usa un horario normal de referencia (por ejemplo 08:00 a 17:00).

- Si NO activas horario personalizado: se usa el horario normal.
- Si SI activas horario personalizado: se usan las horas que indiques en esa linea.

Importante:
- Si una linea ya tenia horario personalizado guardado, al abrirla el interruptor aparece activado automaticamente.

## 3) Que significan HT1, HT2 y HVE

### HT1
Horas fuera del horario normal en dias no festivos.

Ejemplo:
- Si trabajas de 07:00 a 09:00, la parte de 07:00 a 08:00 cuenta como extra (HT1).

### HT2
Horas en dia festivo.

Ejemplo:
- Si la linea esta marcada como festiva, el tiempo de trabajo/viaje de esa linea va a HT2.

### HVE
Horas extra de viaje.

Se calcula con los tramos de viaje:
- Ida: hora ida -> hora llegada
- Vuelta: hora vuelta -> hora vuelta llegada

Solo cuenta como HVE la parte del viaje que quede fuera del horario normal/personalizado.

## 4) Como interpreta la aplicacion los tramos horarios

Para cada tramo (viaje o trabajo), la aplicacion mira:

1. Si empieza antes de la hora de entrada.
2. Si termina despues de la hora de salida.
3. Si cruza medianoche.

Y con eso obtiene las horas que son extra.

## 5) Resumen por categoria profesional

Ademas de los totales generales, la aplicacion genera un resumen por categoria profesional (por ejemplo Oficial, Tecnico, etc.).

Como lo hace:

1. Toma los valores de cada linea.
2. Mira que tecnicos hay asignados y su categoria.
3. Reparte/acumula esas horas en la categoria correspondiente.

Resultado:
- Puedes ver cuanto corresponde a cada categoria en el apartado Resumen.

## 6) Totales del parte que veras en pantalla

Al guardar lineas, se actualizan automaticamente:

- Horas de viaje
- Horas de trabajo
- HT1
- HT2
- HVE
- Kms
- Medias dietas
- Dietas completas
- Hotel

## 7) Recomendaciones para introducir datos correctamente

1. Rellena siempre horas de inicio y fin en cada tramo que aplique.
2. Marca Festivo solo cuando corresponda.
3. Usa horario personalizado solo cuando la linea tenga un horario distinto al normal.
4. Asigna correctamente los tecnicos para que el resumen por categoria sea correcto.
5. Revisa el bloque Resumen antes de cerrar el parte.

## 8) Preguntas frecuentes

### He cambiado una linea y no veo el total actualizado
Normalmente se actualiza al guardar la linea. Si no lo ves al instante, recarga la pantalla del parte.

### Puedo guardar un parte sin firmar
Si. La firma no es obligatoria para guardar cambios normales. Se firma cuando el tecnico lo decida.

### Que pasa con partes antiguos
Si un parte antiguo no tenia resumen por categoria guardado, la aplicacion lo puede recalcular al vuelo al mostrarlo.
