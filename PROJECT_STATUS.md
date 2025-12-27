# Estado del Proyecto - Campaña Chadan Rosado Taylor 2026
**Fecha:** 26/12/2025
**Última sesión:** Despliegue en Producción (Hostinger) y Ajustes Finales.

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
*   **Reporte Electoral Censo:**
    *   Se corrigió la funcionalidad para agrupar por Zonas/Puestos.
    *   Se ajustó el nombre del controlador a `ReporteElectoralCenso.php` (CamelCase) y se actualizó el enlace en `nav_admin.php` para coincidir.
*   **Correo Electrónico:**
    *   Se habilitó `PHPMailer` en la API.
    *   `Helpers.php` de la API fue modificado para cargar las librerías solo cuando se llama a `sendEmail`.
*   **CORS:** Se habilitaron cabeceras CORS en `api-votes/Controllers/Contacto.php` para permitir peticiones desde la Landing.

## 3. Próximos Pasos (Plan de Trabajo Futuro)
Para la siguiente sesión se tiene planeado:

1.  **Dashboard con Gráficas:**
    *   Implementar visualización gráfica en el Dashboard principal.
    *   **Métricas:** Potencial Electoral vs Electores Registrados.
    *   **Desglose:** Por Departamento, Municipio, Zona y Puesto.
2.  **Módulo Contable:**
    *   Creación de nuevas páginas para la gestión financiera de la campaña (ingresos/gastos).

## 4. Notas para el Desarrollador
*   Al subir nuevos controladores, **SIEMPRE verificar mayúsculas/minúsculas**.
*   Si falla el envío de correos, verificar credenciales SMTP en `api-votes/Helpers/Helpers.php`.
*   La Landing Page es un HTML estático que depende de la disponibilidad del Admin (para assets) y la API (para datos).

---
*Guardado por Antigravity - 26 Dic 2025*
