**Fecha:** 04/01/2026 (Tarde)
**Última sesión:** Refactorización Global de JavaScript y Estandarización de API Financiera.

## 1. Arquitectura del Sistema (Estándar 2026)
El sistema opera bajo una arquitectura desacoplada Frontend-Backend con comunicación vía JSON RESTful.

*   **Frontend (Admin):**
    *   **Proyecto:** `app-votes`
    *   **Tecnología:** PHP (Vistas) + Vanilla JS (Lógica).
    *   **Autenticación:** JWT almacenado en `localStorage`.
    *   **Núcleo JS:** `functions_admin.js` aloja los helpers globales:
        *   `fetchData(url, method, body)`: Manejador central de peticiones con auto-inyección de Token y manejo de errores.
        *   `getDataTableFetchConfig(endpoint)`: Generador de configuración estandar para DataTables (AJAX + Auth).
        *   `lenguajeEspanol`: Configuración global para DataTables.
*   **Backend (API):**
    *   **Proyecto:** `api-votes`
    *   **Tecnología:** PHP MVC.
    *   **Estándar de Respuesta:** `{ "status": true/false, "msg": "...", "data": [...] }`
    *   **Seguridad:** Validación de Tokens JWT en encabezado `Authorization: Bearer ...`.

---

## 2. Cambios Recientes
### A. Refactorización Mayor (Frontend)
1.  **Estandarización de DataTables (`getDataTableFetchConfig`):**
    *   Implementado en todos los módulos (`Usuarios`, `Líderes`, `Candidatos`, `Roles`, `Entradas`, `Salidas`, `Movimientos`, `Conceptos`, `Elementos`, `Terceros`, `Electores`).
    *   Elimina configuración manual AJAX redundante y asegura envío consistente del Token JWT.
2.  **Optimización de API Calls (`fetchData`):**
    *   Todas las llamadas `fetch` nativas reemplazadas por el helper global `fetchData`.
    *   Manejo centralizado de parseo JSON y errores 401 (Sesión Expirada).

### B. Estandarización Backend (Componente Financiero)
*   **Corrección de Controladores API:**
    *   Se actualizaron `Entradas`, `Salidas`, `Conceptos`, `Elementos` y `Terceros` para devolver la estructura JSON estándar `{status: true, data: [...]}` en lugar de arrays planos.
    *   Esto garantiza compatibilidad total con el helper JS `getDataTableFetchConfig`.

### C. Documentación
*   Creado flujo de trabajo `.agent/workflows/js_standards.md` detallando los nuevos estándares de desarrollo JS.

## 3. Estado de Módulos
*   **Dashboard:**
    *   [OK] Gráficas y Métricas.
*   **Gestión Administrativa:**
    *   [OK] Usuarios, Roles.
    *   [OK] Terceros, Conceptos, Elementos.
    *   [OK] Entradas, Salidas.
*   **Gestión Financiera:**
    *   [OK] Movimientos (CRUD Completo).
    *   [OK] Informes Financieros (Saldos/Kardex).
    *   [OK] Informe de Ingresos y Gastos.
*   **Gestión Electoral:**
    *   [OK] Líderes, Candidatos.
    *   [OK] Electores (Validación Documento).
    *   [OK] Votación (Control de Duplicidad).
*   **Reportes y Análisis:**
    *   [OK] Monitor Día D (Tiempo Real).
    *   [OK] Análisis E-14 (Auditoría).
    *   [OK] Reporte Electoral Censo.

## 4. Notas Técnicas
*   **DataTables:** Siempre usar `"ajax": getDataTableFetchConfig('/endpoint')`. No usar spread operator `...` directamente en la raíz de la configuración.
*   **API Response:** Cualquier nuevo endpoint de listado debe retornar `{ "status": true, "data": [] }` para ser consumido correctamente por el frontend.
*   **Impresión:** Los reportes ahora incluyen clases `d-print-none` y estilos `@media print` para asegurar una salida limpia en papel o PDF.

---
*Bitácora Actualizada - Antigravity*
