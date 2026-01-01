**Fecha:** 01/01/2026 (Tarde)
**Última sesión:** Implementación de Informe de Movimientos & Correcciones Finales.

## 1. Arquitectura del Sistema (Estándar 2026)
El sistema opera bajo una arquitectura desacoplada Frontend-Backend con comunicación vía JSON RESTful.

*   **Frontend (Admin):**
    *   **Proyecto:** `app-votes`
    *   **Tecnología:** PHP (Vistas) + Vanilla JS (Lógica).
    *   **Autenticación:** JWT almacenado en `localStorage`.
    *   **Núcleo JS:** `functions_admin.js` aloja los helpers globales:
        *   `fetchData(url, method, body)`: Manejador central de peticiones con auto-inyección de Token y manejo de errores.
        *   `lenguajeEspanol`: Configuración global para DataTables.
*   **Backend (API):**
    *   **Proyecto:** `api-votes`
    *   **Tecnología:** PHP MVC.
    *   **Seguridad:** Validación de Tokens JWT en encabezado `Authorization: Bearer ...`.

---

## 2. Cambios Recientes
### A. Nuevas Funcionalidades
1.  **[NUEVO] Informe de Ingresos y Gastos (`Infmovimientos`):**
    *   Filtros por Rango de Fechas y Concepto.
    *   Resumen Financiero (Ingresos vs Gastos = Balance) con indicadores visuales.
    *   Optimización para impresión (diseño limpio y horizontal).
2.  **Análisis E-14 (Auditoría):**
    *   Agregado de **Tarjetas de Resumen (Boxes)** idénticas al Monitor Día D.

### B. Correcciones Críticas & Refactorización
*   **Votación:** Implementada validación estricta (Frontend/Backend) para prevenir votos duplicados; corregido bucle infinito en la UI.
*   **JavaScript Global:** Estandarización masiva completada, eliminando redundancias y mejorando el rendimiento de importaciones.

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
*   **Depuración:** Si aparece `SyntaxError: Identifier '...' has already been declared`, revisar si el módulo JS está importando una definición local que ya existe en `functions_admin.js`.
*   **Impresión:** Los reportes ahora incluyen clases `d-print-none` y estilos `@media print` para asegurar una salida limpia en papel o PDF.

---
*Bitácora Actualizada - Antigravity*
