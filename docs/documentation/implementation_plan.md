# Plan de Importación de Puestos de Votación

El objetivo es actualizar la base de datos MySQL con la información reciente de puestos de votación proporcionada en formato CSV.

# Goal Description
Importar datos del archivo `import_data.csv` a las tablas `zones`, `puestos`, y `mesas` de la base de datos `votes`, asegurando la integridad referencial con la configuración geográfica (`Config.json`).

## User Review Required
> [!IMPORTANT]
> Se asume que la columna `zz` (Zona) del CSV corresponde a una zona que debe existir o crearse en la tabla `zones`. Dado que no se ha confirmado una columna `code_zone` en la tabla `zones`, se utilizará el nombre de la comuna (columna `comuna`) para identificar la zona.
> Se asume que `num_puesto` en la tabla `puestos` corresponde a la columna `pp` del CSV.

## Proposed Changes

### Scripts
#### [NEW] [import_script.php](file:///c:/xampp/htdocs/votes/import_script.php)
Crear un script PHP independiente que realice la importación:
1.  **Cargar Configuración**: Leer `api-votes/Json/Config.json` para mapear Nombres de Departamentos/Municipios a sus IDs.
2.  **Leer CSV**: Procesar `import_data.csv`.
3.  **Lógica de Importación (por fila)**:
    *   **Validar Municipio**: Buscar el ID del municipio (ej: "SANTA MARTA" -> 569).
    *   **Gestionar Zona**:
        *   Buscar en tabla `zones` si existe una zona con el nombre indicado en columna `comuna` (o `zz` si aplica).
        *   Si no existe, insertar nueva zona.
    *   **Gestionar Puesto**:
        *   Buscar en tabla `puestos` por `num_puesto` (columna `pp`) y `idzona_puesto`.
        *   Si no existe, insertar (nombre, dirección, num_puesto).
        *   Si existe, actualizar nombre y dirección.
    *   **Gestionar Mesas**:
        *   Leer columna `mesas` (cantidad).
        *   Verificar si existen las mesas 1 a N para ese puesto.
        *   Insertar las mesas faltantes.

## Verification Plan

### Automated Tests
*   **Ejecución del Script**: Correr `php import_script.php` y capturar la salida para verificar inserciones/actualizaciones.
*   **Inspección DB**: Usar un script de lectura (`check_stats.php`) que cuente puestos y mesas por municipio antes y después de la importación para confirmar el cambio.
*   **Verificación Manual**: El usuario puede revisar en el aplicativo si los nuevos puestos aparecen.
