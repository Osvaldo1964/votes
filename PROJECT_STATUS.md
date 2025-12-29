# Estado del Proyecto - Campaña Chadan Rosado Taylor 2026
**Fecha:** 28/12/2025 (Pausa Almuerzo)
**Última sesión:** Implementación Informe de Saldos, Corrección de Menú y Helper Global de Diseño.

## 1. Arquitectura del Sistema (Desacoplada)
El sistema ha sido dividido en tres componentes independientes para su despliegue en Hostinger:

*   **Landing Page (Pública):**
    *   **Dominio:** `chadanalacamara.com`
    *   **Ubicación:** Raíz del hosting (`public_html`).
    *   **Archivos:** `index.html` (Bootstrap + JS Vanilla).
    *   **Assets:** Consume CSS/JS desde `admin.chadanalacamara.com/Assets/` para evitar duplicidad.
    *   **Funcionalidad:** Consulta de puesto (API) y Formulario de Contacto (API).

*   **Admin (Aplicación Web):**
    *   **Dominio:** `admin.chadanalacamara.com`
    *   **Ubicación:** `public_html/admin/` (o subdominio asignado).
    *   **Código:** Carpeta `app-votes`.
    *   **Cambios Clave:**
        *   `Controllers/Home.php` redirige a Login/Dashboard.
        *   `Config/Config.php` debe tener `BASE_URL` con HTTPS.
        *   **Importante:** En Linux (Hostinger), respetar Mayúsculas/Minúsculas en nombres de archivos y controladores (Ej: `ReporteElectoralCenso.php`).

*   **API (Backend):**
    *   **Dominio:** `api.chadanalacamara.com`
    *   **Ubicación:** `public_html/api/` (o subdominio asignado).
    *   **Código:** Carpeta `api-votes`.
    *   **Funciones:** Auth JWT, endpoints de Reportes, Consulta Puesto (`Place`), Contacto (`Contacto`).
    *   **Correo:** Se implementó `sendEmail` en `Helpers.php` usando PHPMailer.

## 2. Ajustes Recientes y Puntos Críticos
*   **Menú Navegación (`nav_admin.php`):**
    *   **Corrección Lógica:** Solucionado conflicto treeview niveles 2 y 3.
    *   **Estética:** Iconos actualizados y mejor indentación.
    *   **Pendiente:** Color "rosado suave" para ítems activos.
*   **Reporte de Saldos (`Infsaldos`):**
    *   Implementado reporte General (Valorizado) y Detallado (Kardex).
    *   Usa el nuevo **Helper Global** `fntGetHeaderReporte()` en `main.js` para estandarizar encabezados.
    *   Se agregó caché busting (`?v=2`) en `template/header_admin.php` y `footer_admin.php` para asegurar carga de nuevos JS/CSS.
*   **Reporte Electoral Censo:**
    *   Agrupación por Zonas/Puestos corregida.

## 3. Estado de Módulos
*   **Dashboard:**
    *   **COMPLETADO:** Visualización gráfica implementada y funcional.
*   **Contabilidad (Gestión):**
    *   **COMPLETADO:** CRUDs de Terceros, Conceptos, Elementos, Movimientos, Entradas, Salidas.
*   **Contabilidad (Reportes):**
    *   **COMPLETADO:** Informe Elementos (Saldos/Kardex).
    *   **PENDIENTE:** Informe de Ingresos y Gastos (`Infmovimientos`).

## 4. Próximos Pasos (Al Regreso)
1.  **Informe de Ingresos y Gastos (`Infmovimientos`):**
    *   Crear backend y frontend para ver el flujo de caja.
    *   Integrar con `fntGetHeaderReporte()`.
2.  **Validación General:**
    *   Revisión final de estilos y funcionamiento en producción.

## 4. Notas para el Desarrollador
*   Al subir archivos a Hostinger, si hay problemas de JS/CSS, verificar si se agregó `?v=X` o limpiar caché del navegador.
*   La función global de reporte está en `main.js`, asegurarse siempre de que este archivo esté actualizado en el servidor.

---
*Guardado por Antigravity - 28 Dic 2025 - 15:00*
