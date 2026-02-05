# Guía de Actualización al Hosting (05/02/2026)

Este documento detalla los archivos modificados y creados durante la sesión de hoy, los cuales deben ser subidos al servidor de producción.

## 1. Backend (API)
Subir estos archivos a la carpeta `api-votes/` en el hosting:
*   `api-votes/Controllers/Electores.php` - *Lógica de IDs para cascada y registro sincronizado.*
*   `api-votes/Controllers/Lugares.php` - *Protección contra respuestas no-JSON en puestos/mesas.*
*   `api-votes/Models/ElectoresModel.php` - *Consultas relacionales y lógica de sincronización con `places`.*
*   `api-votes/Models/ResultadosModel.php` - *Corrección de error de parámetros en consulta de mesas.*

## 2. Frontend (App Adminsitrativa)
Subir estos archivos a la carpeta `app-votes/` en el hosting:
*   `app-votes/Views/Template/Modals/modalElectores.php` - *Interfaz con selectores de Zona/Puesto/Mesa.*
*   `app-votes/Assets/js/functions_electores.js` - *Motor de cascada y búsqueda inteligente de cédula.*
*   `app-votes/Assets/js/functions_resultados.js` - *Validaciones de seguridad para fluidez en resultados.*

## 3. Base de Datos
Para actualizar la infraestructura geográfica (Zonas, Puestos y Mesas):
*   **Archivo:** `act_votos.csv` (Subir a la raíz para que el script lo encuentre).
*   **Script de ejecución:** `docs/scripts/import_script.php`.
*   **Instrucciones:** Ejecutar el script desde la línea de comandos en el servidor: `php docs/scripts/import_script.php` o cargarlo vía navegador si el hosting lo permite.

## 4. Documentación de Referencia
Arquivos informativos sobre el estado actual:
*   `PROJECT_STATUS.md`
*   `docs/documentation/modulo_electores_v2.md`
*   `docs/scripts/` (Se recomienda subir toda la carpeta para tener las herramientas de diagnóstico a mano).

---
**Recomendación:** Siempre realizar un backup de los archivos `ElectoresModel.php` y `functions_electores.js` actuales del hosting antes de sobreescribirlos.
