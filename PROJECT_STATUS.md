**Fecha:** 05/01/2026 (Noche)
**Última sesión:** Implementación Módulo Testigos Electorales.

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
    *   Implementado en todos los módulos (`Usuarios`, `Líderes`, `Candidatos`, `Roles`, `Entradas`, `Salidas`, `Movimientos`, `Conceptos`, `Elementos`, `Terceros`, `Electores`, `Testigos`).
    *   Elimina configuración manual AJAX redundante y asegura envío consistente del Token JWT.
2.  **Optimización de API Calls (`fetchData`):**
    *   Todas las llamadas `fetch` nativas reemplazadas por el helper global `fetchData`.
    *   Manejo centralizado de parseo JSON y errores 401 (Sesión Expirada).

### B. Estandarización Backend (Componente Financiero)
*   **Corrección de Controladores API:**
    *   Se actualizaron `Entradas`, `Salidas`, `Conceptos`, `Elementos` y `Terceros` para devolver la estructura JSON estándar `{status: true, data: [...]}` en lugar de arrays planos.
    *   Esto garantiza compatibilidad total con el helper JS `getDataTableFetchConfig`.

### C. Implementación Módulo Testigos
1.  **Backend (API):**
    *   Modelo `TestigosModel` con lógica de asignación y verificación de duplicados.
    *   Controlador `Testigos` con validación de permisos robusta (Token + Fallback GET).
    *   Ajuste en `LugaresModel` para devolver `id_place` agrupado, permitiendo guardar la ubicación correctamente.
2.  **Frontend (App):**
    *   Vista `testigos.php` con DataTable integrado y botones de acción dinámicos.
    *   Modal `modalTestigos.php` con carga asíncrona de electores y cascada de ubicación.
    *   **Nuevo:** Selector múltiple de "Mesas de Votación" (`listMesas`) que permite asignar varias mesas de un Puesto a un solo testigo.
    *   **Nuevo:** Lógica inteligente que muestra mesas "Libres" + "Asignadas al testigo actual", evitando conflictos.
    *   Uso de `data-size` y `selected-text-format` para optimizar UX en selectores múltiples.
    *   Uso de `getModal` para inclusión correcta de templates.

### E. Optimizaciones y Correcciones (Enero 2026)
1.  **Dashboard:**
    *   **Gráfico Top Líderes:** Actualizado con paleta de colores dinámica y etiquetas inteligentes (cantidad + porcentaje) dentro/fuera de las barras según el espacio disponible.
    *   **Cálculos:** Implementado cálculo correcto de porcentajes basado en el total desplegado.
2.  **Módulo Candidatos:**
    *   **Corrección Bug:** Solucionado error en eliminación de candidatos (`idrol` vs `idcandidato` en API).
3.  **Reporte Testigos Electorales:**
    *   **Nueva Funcionalidad:** Detección automática de **"Mesas Sin Asignar"** (Huérfanas).
    *   **Lógica:** Al generar un reporte por Puesto, el sistema escanea `headresultado` y `places` para identificar mesas que no tienen testigo asignado en esa ubicación.
    *   **Visualización:** Se agrega una fila final resaltada (rojo) con el listado de todas las mesas pendientes por cubrir.
    *   **Corrección SQL:** Ajuste en el Modelo para filtrar correctamente por Nombre de Puesto, Zona y Municipio, resolviendo el problema de agrupación de mesas.

## 3. Estado de Módulos
*   **Dashboard:**
    *   [OK] Gráficas y Métricas (Optimizado).
*   **Gestión Administrativa:**
    *   [OK] Usuarios, Roles.
    *   [OK] Terceros, Conceptos, Elementos.
    *   [OK] Entradas, Salidas.
*   **Gestión Financiera:**
    *   [OK] Movimientos (CRUD Completo).
    *   [OK] Informes Financieros (Saldos/Kardex).
    *   [OK] Informe de Ingresos y Gastos.
*   **Gestión Electoral:**
    *   [OK] Líderes, Candidatos (Corregido).
    *   [OK] Electores (Validación Documento).
    *   [OK] Votación (Control de Duplicidad).
    *   [OK] **Testigos Electorales** (Asignación Múltiple, Reporte con Auditoría de Vacíos).
*   **Reportes y Análisis:**
    *   [OK] Monitor Día D (Tiempo Real).
    *   [OK] Análisis E-14 (Auditoría).
    *   [OK] Reporte Electoral Censo.

## 4. Notas Técnicas
*   **DataTables:** Siempre usar `"ajax": getDataTableFetchConfig('/endpoint')`. No usar spread operator `...` directamente en la raíz de la configuración.
*   **API Response:** Cualquier nuevo endpoint de listado debe retornar `{ "status": true, "data": [] }` para ser consumido correctamente por el frontend.
*   **Impresión:** Los reportes ahora incluyen clases `d-print-none` y estilos `@media print` para asegurar una salida limpia en papel o PDF.
*   **Lugares/Puestos - Lógica Crítica:**
    *   La tabla `places` representa el **Censo Electoral** (1 fila = 1 elector en una mesa).
    *   La tabla `headresultado` representa las **Mesas Únicas** (1 fila = 1 mesa física).
    *   Para buscar mesas, SIEMPRE consultar `headresultado` y hacer JOIN con `places` para obtener metadatos.
    *   Para agrupar mesas de un puesto, usar `p.nameplace_place` + `p.idzona_place` + `p.idmuni_place`, **NUNCA** `id_place` (ya que este varía por elector).

## 5. Próximos Pasos (Roadmap)
*   **URGENTE - Mañana:** **Refactorización Estructural Crítica (Normalización BD)**
    *   **Contexto:** La tabla `places` (Censo) está desnormalizada. Contiene datos repetidos de ubicación para cada elector.
    *   **Meta:** Extraer las 783 mesas únicas identificadas a una tabla maestra (probablemente adecuada en `headresultado` o nueva `mesas`), dejando `places` solo para electores vinculados por ID.
    *   **Impacto:** Reducción de 265k+ registros repetidos a 783 únicos para consultas de ubicación. Preparación para carga masiva de 800k electores.
    *   **Procedimiento (Producción):**
        1.  Backup completo.
        2.  Script de migración SQL (`INSERT INTO maestras SELECT DISTINCT...`).
        3.  Actualización de esquema (`ALTER TABLE places`).
        4.  Refactorización masiva de Modelos PHP (`JOIN` en lugar de lectura directa).
        5.  Deploy rápido (Ventana de mantenimiento).

*   **App Móvil (React Native/Expo):**
    *   Objetivo: Crear aplicación para Android/iOS reutilizando la API `api-votes`.
    *   Alcance inicial: 3 Módulos clave (por definir).
    *   Complejidad estimada: Media-Baja (Reutilización del 70% de lógica Backend).

---
*Bitácora Actualizada - Antigravity*
