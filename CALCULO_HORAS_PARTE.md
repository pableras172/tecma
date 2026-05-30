# Resumen de calculo de horas en Partes de Trabajo

Este documento resume como se calculan las horas en lineas de parte y como se agregan al parte.

## 1) Flujo general

1. Se crean o editan lineas en el RelationManager de `LineaParteTrabajo`.
2. Al guardar/editar/borrar una linea se ejecuta `recalcularTotales()` del parte.
3. `recalcularTotales()`:
   - Recalcula resumen por categoria profesional desde las lineas.
   - Persiste filas en `resumen_parte_trabajo_categorias`.
   - Actualiza campos legacy del parte (`total_horas_viaje`, `total_ht1`, etc.).

Referencias:
- `app/Filament/Resources/ParteTrabajoResource/RelationManagers/LineasParteTrabajoRelationManager.php`
- `app/Filament/Personal/Resources/ParteTrabajoResource/RelationManagers/LineasParteTrabajoRelationManager.php`
- `app/Models/ParteTrabajo.php`

## 2) Calculo en cada linea

### 2.1 Horario base y horario personalizado

- Horario base por defecto:
  - `hora_entrada`: `setting('horarios.hora_entrada', '08:00')`
  - `hora_salida`: `setting('horarios.hora_salida', '17:00')`
- Si el switch `personalizar_horario` esta activo y hay `hora_entrada_pers` / `hora_salida_pers`, el sistema sincroniza:
  - `hora_entrada = hora_entrada_pers`
  - `hora_salida = hora_salida_pers`
- Si se desactiva el switch, las horas personalizadas se limpian y se vuelve al horario base.

Metodo clave:
- `sincronizarHorarioBase(Set $set, Get $get)`

### 2.2 Regla de horas extra de un rango

Metodo:
- `calcularHorasExtraRango(string $horaInicio, string $horaFin, ?Get $get = null): float`

Regla:
- Se considera extra todo lo que quede fuera del tramo `[hora_entrada, hora_salida]`.
- Se calcula:
  - Extra antes de entrada: intervalo entre `horaInicio` y `min(horaFin, hora_entrada)` si empieza antes.
  - Extra despues de salida: intervalo entre `max(horaInicio, hora_salida)` y `horaFin` si termina despues.
- Si el rango cruza medianoche (`horaFin < horaInicio`), se suma un dia a `horaFin`.

### 2.3 HT1 y HT2

Metodo:
- `recalcularHorasExtrasLinea(Set $set, Get $get, ?bool $esFestivo = null)`

Intervalos evaluados:
- Ida: `hora_ida -> hora_llegada`
- Vuelta: `hora_vuelta -> hora_vuelta_llegada`
- Trabajo 1: `hora_inicio_trabajo -> hora_fin_trabajo`
- Trabajo 2: `hora_inicio_trabajo_2 -> hora_fin_trabajo_2`

Regla:
- Si `esfestivo = true`:
  - Todo el tiempo de esos intervalos va a `ht2` (duracion total del rango).
  - `ht1` queda en 0 para esos tramos.
- Si `esfestivo = false`:
  - `ht1` suma solo horas extra fuera del horario base/personalizado.
  - `ht2` no suma por esos tramos.

### 2.4 HVE (horas extra de viaje)

Metodo:
- `calcularHVE(Set $set, Get $get)`

Regla:
- Suma horas extra (fuera de horario) de:
  - Ida (`hora_ida -> hora_llegada`)
  - Vuelta (`hora_vuelta -> hora_vuelta_llegada`)

Resultado:
- `hve = extra_ida + extra_vuelta` (redondeado a 2 decimales).

## 3) Agregacion en el parte

Metodo:
- `ParteTrabajo::recalcularTotales()`

### 3.1 Resumen por categoria profesional

Metodo base:
- `calcularResumenPorCategoriaDesdeLineas()`

Proceso:
1. Carga lineas con usuarios y categoria profesional.
2. Para cada linea calcula valores base:
   - `horas_viaje` normales (viaje total - viaje extra)
   - `horas_trabajo` normales (trabajo total - trabajo extra)
   - `ht1`, `ht2`, `hve` de la linea
3. Agrupa usuarios de la linea por `categoria_profesional_id`.
4. Multiplica los valores de la linea por el numero de usuarios de cada categoria (`factor`).
5. Acumula por categoria en memoria.
6. Redondea y ordena por nombre de categoria.

Persistencia:
- Se borran filas previas del resumen.
- Se insertan nuevas filas en `resumen_parte_trabajo_categorias`.

### 3.2 Totales legacy del parte

Despues de persistir resumen por categoria, se actualizan:
- `total_horas_viaje` = suma de `horas_viaje` del resumen
- `total_horas_trabajo` = suma de `horas_trabajo`
- `total_ht1` = suma de `ht1`
- `total_ht2` = suma de `ht2`
- `total_hve` = suma de `hve`
- `total_km` = suma de `kms` de lineas
- `total_media_dieta` = numero de lineas con `media_dieta = true`
- `total_dieta` = numero de lineas con `dieta_completa = true`
- `total_hotel` = numero de lineas con `hotel = true`

## 4) Lectura del resumen para UI/PDF

Metodo:
- `obtenerResumenPorCategoria()`

Regla:
- Si hay filas persistidas en `resumen_parte_trabajo_categorias`, usa esas.
- Si no hay (compatibilidad con datos antiguos), recalcula al vuelo desde lineas.

## 5) Notas importantes

- La base de datos de lineas usa campos `hora_inicio_trabajo_2` y `hora_fin_trabajo_2`.
- En el panel personal, los campos del formulario coinciden con esos nombres.
- En el panel admin, conviene mantener exactamente esos nombres de campo para evitar desajustes en el calculo.

## 6) Resumen rapido de formulas

- `extra(rango) = extra_antes_entrada + extra_despues_salida`
- `hve = extra(ida) + extra(vuelta)`
- Dia festivo:
  - `ht2 += duracion_total(intervalo)`
- Dia laborable:
  - `ht1 += extra(intervalo)`
- Horas normales viaje/trabajo en resumen por categoria:
  - `horas_normales = horas_totales - horas_extra`
- Acumulacion por categoria:
  - `valor_categoria += valor_linea * numero_usuarios_categoria_en_linea`
