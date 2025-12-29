# Estado del Proyecto - Campaña Chadan Rosado Taylor 2026
**Fecha:** 29/12/2025 (Mañana)
**Última sesión:** Implementación Informe de Electores (Desacoplado API/App).

## 1. Arquitectura del Sistema (Desacoplada)
El sistema ha sido dividido en tres componentes independientes para su despliegue en Hostinger:

*   **Landing Page (Pública):**
    *   **Dominio:** `chadanalacamara.com`
    *   **Ubicación:** Raíz del hosting (`public_html`).
    *   **Archivos:** `index.html` (Bootstrap + JS Vanilla).
    *   **Assets:** Consume CSS/JS desde `admin.chadanalacamara.com/Assets/` para evitar duplicidad.
    *   **Funcionalidad:** Consulta de puesto (API) y Formulario de Contacto (API).

*   **Admin (Aplicación Web FRONTEND):**
    *   **Dominio:** `admin.chadanalacamara.com`
    *   **Ubicación:** `public_html/admin/` (o subdominio asignado).
    *   **Código:** Carpeta `app-votes`.
    *   **Cambios Clave:**
        *   `Controllers` ahora solo renderizan Vistas.
        *   Toda la lógica de datos se consume vía JS (`fetch`) hacia la API.
        *   **Importante:** En Linux (Hostinger), respetar Mayúsculas/Minúsculas en nombres de archivos y controladores (Ej: `ReporteElectoralCenso.php`).

*   **API (Backend BACKEND):**
    *   **Dominio:** `api.chadanalacamara.com`
    *   **Ubicación:** `public_html/api/` (o subdominio asignado).
    *   **Código:** Carpeta `api-votes`.
    *   **Funciones:** Auth JWT, endpoints de Reportes, Consulta Puesto (`Place`), Contacto (`Contacto`), **Infelectores**.
    *   **Correo:** Se implementó `sendEmail` en `Helpers.php` usando PHPMailer.

## 2. Ajustes Recientes y Puntos Críticos
*   **[NUEVO] Informe de Electores (`Infelectores`):**
    *   Implementado patrón API + Frontend.
    *   **Backend (`api-votes`):** Controlador `Infelectores.php` y Modelo `InfelectoresModel.php`.
    *   **Frontend (`app-votes`):** Vista con filtro ("Todos" por defecto) y agrupación por Líderes con subtotales.
*   **Menú Navegación (`nav_admin.php`):**
    *   **Corrección Lógica:** Solucionado conflicto treeview niveles 2 y 3.
    *   **Estética:** Iconos actualizados y mejor indentación.
*   **Reporte de Saldos (`Infsaldos`):**
    *   Implementado reporte General (Valorizado) y Detallado (Kardex).
    *   Usa el nuevo **Helper Global** `fntGetHeaderReporte()` en `main.js`.

## 3. Estado de Módulos
*   **Dashboard:**
    *   **COMPLETADO:** Visualización gráfica implementada y funcional.
*   **Contabilidad (Gestión):**
    *   **COMPLETADO:** CRUDs de Terceros, Conceptos, Elementos, Movimientos, Entradas, Salidas.
*   **Contabilidad (Reportes):**
    *   **COMPLETADO:** Informe Elementos (Saldos/Kardex).
    *   **PENDIENTE:** Informe de Ingresos y Gastos (`Infmovimientos`).
*   **Electoral (Reportes):**
    *   **COMPLETADO:** Informe de Electores (Agrupado por Líder).
    *   **EN PROCESO:** Reporte Electoral Censo.

## 4. Próximos Pasos
1.  **Informe de Ingresos y Gastos (`Infmovimientos`):**
    *   Crear backend y frontend para ver el flujo de caja.
    *   Integrar con `fntGetHeaderReporte()`.
2.  **Validación General:**
    *   Revisión final de estilos y funcionamiento en producción.

## 5. Notas para el Desarrollador
*   **Despliegue Config:** Recordar que `Config.php` en local puede diferir de producción (rutas absolutas vs dominios). No sobrescribir credenciales de producción al subir si no es necesario.
*   **Caché:** Al subir archivos a Hostinger, si hay problemas de JS/CSS, verificar si se agregó `?v=X` o limpiar caché del navegador.

---
*Guardado por Antigravity - 29 Dic 2025*
