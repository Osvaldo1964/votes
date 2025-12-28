# Estado del Proyecto - Campaña Chadan Rosado Taylor 2026
**Fecha:** 28/12/2025
**Última sesión:** Corrección Menú Navegación (Niveles 2 y 3) y Mejoras Estéticas.

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
    *   **Correo:** Se implementó `sendEmail` en `Helpers.php` usando PHPMailer (librería subida manualmente a `Libraries/phpmailer`).

## 2. Ajustes Recientes y Puntos Críticos
*   **Menú Navegación (`nav_admin.php`):**
    *   **Corrección Lógica:** Se solucionó el problema donde todos los submenús se abrían al mismo tiempo. Se actualizaron los selectores CSS (`>`) en `main.css` y se agregó `stopPropagation` en `main.js`.
    *   **Estética:** Se actualizaron iconos de Nivel 2 (Administrativos, Electorales, Financieros) para ser más semánticos.
    *   **Indentación:** Se agregó padding extra a los ítems de Nivel 3 para mejorar la jerarquía visual.
    *   *Nota:* Queda pendiente revisión de color de fondo "rosado suave" para ítems activos (actualmente negro).
*   **Reporte Electoral Censo:**
    *   Se corrigió la funcionalidad para agrupar por Zonas/Puestos.
    *   Se ajustó el nombre del controlador a `ReporteElectoralCenso.php`.
*   **Correo Electrónico:**
    *   Se habilitó `PHPMailer` en la API.
*   **CORS:** Se habilitaron cabeceras CORS en `api-votes/Controllers/Contacto.php`.

## 3. Estado de Módulos
*   **Dashboard:**
    *   **COMPLETADO:** Visualización gráfica implementada (Widgets, Gráfica de Barras Líderes, Gráfica Doughnut Municipios) consumiendo API.
*   **Contabilidad (Gestión):**
    *   **COMPLETADO:** Controladores y vistas base para Terceros, Conceptos, Elementos, Entradas, Salidas y Movimientos.
*   **Contabilidad (Reportes):**
    *   **PENDIENTE:** Desarrollo de controladores y vistas para `Infsaldos` (Informe Saldos) e `Infmovimientos` (Informe Movimientos), actualmente enlazados en el menú pero inexistentes en backend.

## 4. Próximos Pasos Inmediatos
1.  **Reportes Financieros:**
    *   Crear controlador y vista para `Infsaldos`.
    *   Crear controlador y vista para `Infmovimientos`.
    *   Asegurar que consuman correctamente los datos de la API.

## 4. Notas para el Desarrollador
*   Al subir nuevos controladores, **SIEMPRE verificar mayúsculas/minúsculas**.
*   Si falla el envío de correos, verificar credenciales SMTP en `api-votes/Helpers/Helpers.php`.
*   La Landing Page es un HTML estático que depende de la disponibilidad del Admin (para assets) y la API (para datos).

---
*Guardado por Antigravity - 28 Dic 2025*
