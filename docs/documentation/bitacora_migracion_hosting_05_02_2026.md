# Bitácora - Migración y Corrección Resultados (05/02/2026)

## 1. Migración de Datos a Hostinger
**Objetivo:** Resolver errores de "tablas padres" al intentar actualizar `puestos` y `mesas` en Hostinger.

### Solución
Se creó un script especializado que exporta los datos locales envueltos en comandos `SET FOREIGN_KEY_CHECKS`. Esto permite reemplazar las tablas en el servidor sin eliminar manualmente las dependencias.

### Uso del Script
1.  **Generar Archivo de Migración:**
    ```bash
    php docs/scripts/generate_migration_sql.php
    ```
    Esto crea `migration_payload.sql`.

2.  **Importar en Hostinger:**
    -   Ir a phpMyAdmin -> Importar.
    -   Subir `migration_payload.sql`.
    -   Ejecutar.

---

## 2. Corrección Módulo "Resultados"
**Objetivo:** Solucionar el problema donde el desplegable de "Mesas" permanecía vacío tras seleccionar un Puesto.

### Diagnóstico
-   **Frontend (`functions_resultados.js`):** Enviaba el **Nombre** del Puesto (`nombrePuesto`) al backend.
-   **Backend (`Lugares.php`):** Esperaba el **ID** del Puesto (`idPuesto`).

### Corrección Aplicada
Se actualizó `app-votes/Assets/js/functions_resultados.js`:
-   **Selector:** `listPuesto` ahora usa `id_place` como su valor.
-   **Llamada API:** `fntGetMesas` ahora envía `idPuesto`.
-   **Potencial:** Lógica actualizada para recuperar el Nombre del Puesto desde el texto del selector (necesario para la consulta `getPotencial`).

### Verificación
-   [x] Seleccionar Departamento -> Municipio -> Zona -> Puesto.
-   [x] Verificar que el desplegable "Mesa" se llena.
-   [x] Verificar que "Potencial" carga correctamente tras seleccionar una mesa.

> [!NOTE]
> Recuerda limpiar la caché del navegador (Ctrl + F5) para cargar el archivo JavaScript actualizado.
