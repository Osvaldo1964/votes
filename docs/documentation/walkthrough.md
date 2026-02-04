# Walkthrough - Importación Completa de Puestos de Votación

Se ha completado la importación total de los datos desde `act_votos.csv`.

## Resumen de Cambios

### Operaciones Realizadas
1.  **Limpieza de Datos**: Se ejecutó `TRUNCATE` en las tablas `mesas`, `puestos` y `zones` para eliminar datos antiguos y reiniciar los contadores de ID.
2.  **Importación Masiva**: Se procesó el archivo completo `act_votos.csv`.
3.  **Corrección de Datos**:
    *   Se aseguró la codificación UTF-8 para nombres con tildes y caracteres especiales (ej: "CAÑO DE PALMA").
    *   Se pobló la columna `dpto_zone` en la tabla `zones` usando la relación con el municipio.
    *   Se asignaron valores por defecto para `codigo_zona` ('') y `status_zone` (1).

### Resultados Finales (Verificados)
Las tablas ahora contienen:
*   **Zonas**: 79 registros.
*   **Puestos**: 248 registros.
*   **Mesas**: 2200 registros.

## Cómo verificar
Ejecutar el script de verificación rápida:
```bash
php verify_counts.php
```
O consultar directamente en base de datos:
```sql
SELECT COUNT(*) FROM mesas;
```
