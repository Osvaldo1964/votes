**Fecha:** 19/01/2026 (Mañana)
**Última sesión:** Verificación de Refactorización Crítica (Normalización BD).

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

### D. Implementación App Móvil (Híbrida)
1.  **Tecnología:** HTML5 + Vanilla JS + Capacitor (Android).
    *   No React Native (Decisión de simplicidad y rapidez).
2.  **Funcionalidades:**
    *   **Consulta Puesto (Público):** Consulta de lugar de votación por cédula conectada al Censo.
    *   **Registro Voto (Público):** Marcación rápida de "Ya Voté".
    *   **Módulo E-14 (Testigos):** Autenticación JWT, Flujo de Selección (Dpto -> Muni -> Zona -> Puesto -> Mesa) y Formulario de Digitación de Votos.
3.  **Ajustes Recientes (17/01/2026):**
    *   **Identidad Visual:** Cambio total de paleta a Rosado Institucional (#e91e63) e incorporación de Logo de Candidato.
    *   **UX/Lógica:** Inputs de votos E-14 ahora permiten valores vacíos (convertidos a 0 automáticamente) para agilizar digitación.
    *   **Infraestructura:** Corrección de problemas CORS y despliegue exitoso de APK conectada a API Producción (`api.chadanalacamara.com`).
4.  **Backend (API):**
    *   Ajuste en `ResultadosModel.php` y `Resultados.php` para manejar respuestas simplificadas y evitar errores de debug al guardar E-14.

### G. Unificación de Landing Page y Admin (17/01/2026)
1.  **Consolidación de Arquitectura:**
    *   Se eliminó la carpeta `landing` y la duplicidad de archivos en la raíz del servidor.
    *   Todo el sistema (Public y Admin) ahora se sirve desde una **única base de código** ubicada en la carpeta `admin` (en local `app-votes`).
2.  **Estrategia de Enrutamiento (.htaccess):**
    *   Implementación de reglas de reescritura en la raíz `public_html` que redirigen el tráfico de `chadanalacamara.com` internamente hacia la carpeta `admin` sin cambiar la URL visible.
3.  **Configuración Dinámica (`Config.php`):**
    *   La constante `BASE_URL` ahora detecta automáticamente el protocolo y el dominio de origen (`HTTP_HOST`), permitiendo que la misma app sirva correctamente los recursos tanto para el dominio principal como para el subdominio admin.
4.  **Correcciones Frontend Público:**
    *   **`functions_votacion_publica.js`:** Se independizó la función `fetchData` para permitir peticiones públicas sin requerir Token JWT ni cargar `functions_admin.js`.
    *   **`functions_landing.js`:** Corrección de errores de sintaxis y limpieza de código duplicado.
    *   **Recursos:** Corrección de rutas de librerías (SweetAlert) en `home.php`.

### H. Optimización Estructural Base de Datos (19/01/2026)
1.  **Normalización Completa:**
    *   **Tablas Maestras:** Se crearon/poblaron las tablas `zones`, `puestos` y `mesas` para manejar la infraestructura física independientemente de los electores.
    *   **Reducción de `places`:** La tabla `places` (Censo) se redujo dramáticamente, eliminando columnas redundantes de texto (dpto, muni, puesto). Ahora se vincula a la mesa vía `id_mesa_new`.
2.  **Impacto:**
    *   Consultas de ubicación (Puestos/Mesas) ahora son instantáneas (búsqueda sobre ~800 registros en lugar de ~265k).
    *   Preparado para soportar carga masiva de nuevos electores sin duplicar metadata de ubicación.
3.  **Refactorización Backend:**
    *   `LugaresModel` y `PlaceModel` actualizados para consumir las nuevas tablas (`FROM puestos`, `FROM mesas`).
    *   Lógica de "Mesas Huérfanas" y reportes ajustada al nuevo esquema relacional.

### I. Geolocalización de Votos (19/01/2026)
1.  **Funcionalidad:**
    *   Se captura la latitud y longitud del dispositivo al momento de registrar el voto (Web Admin y App Móvil).
    *   Datos guardados en columnas `lati_elector` y `long_elector` de la tabla `electores`.
2.  **Implementación:**
    *   **Frontend:** Uso de `navigator.geolocation` con timeout de 5s y alta precisión.
    *   **Backend:** Actualización de Controladores (`Electores`, `Publico`) para procesar las nuevas variables opcionales.

## 3. Estado de Módulos
*   **App Móvil (Android):**
    *   [OK] Generación APK (Debug).
    *   [OK] Conexión API Producción.
    *   [OK] Módulos Publico y Testigo (E-14) funcionales.
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
    *   [OK] Votación (Control de Duplicidad + **Geolocalización GPS**).
    *   [OK] **Testigos Electorales** (Asignación Múltiple, Reporte con Auditoría de Vacíos).
*   **Reportes y Análisis:**
    *   [OK] Monitor Día D (Tiempo Real).
    *   [OK] Análisis E-14 (Auditoría).
    *   [OK] Reporte Electoral Censo.

## 4. Notas Técnicas
*   **DataTables:** Siempre usar `"ajax": getDataTableFetchConfig('/endpoint')`. No usar spread operator `...` directamente en la raíz de la configuración.
*   **API Response:** Cualquier nuevo endpoint de listado debe retornar `{ "status": true, "data": [] }` para ser consumido correctamente por el frontend.
*   **Impresión:** Los reportes ahora incluyen clases `d-print-none` y estilos `@media print` para asegurar una salida limpia en papel o PDF.
*   **Lugares/Puestos - Nueva Lógica (Post-Refactor):**
    *   **`mesas`**: Tabla única de mesas físicas. Llave primaria `id_mesa`.
    *   **`puestos`**: Tabla única de puestos de votación. Contiene `idzona_puesto`.
    *   **`places`**: Tabla de **ELECTORES**. Ya no contiene string de ubicación. Se une a `mesas` vía `id_mesa_new` (FK).
    *   **Consultas:** Para listar infraestructura, SIEMPRE usar `puestos` y `mesas`. Solo unir con `places` si se necesita contar votos/personas.
*   **App Móvil:**
    *   La APP usa `app-movil/www/js/config.js` para definir la URL de la API.
    *   En Producción SIEMPRE asegurar que `ResultadosModel.php` en el servidor coincida con la versión optimizada (sin referencias a debug legacy).

## 5. Próximos Pasos (Roadmap)
*   **URGENTE:** **Refactorización Estructural (Completada 19/01)**
    *   *(Tarea de normalización BD finalizada exitosamente. Ver sección H).*

*   **App Móvil (Fase 2):**
    *   Implementar Cache Local (SQLite) para funcionamiento Offline.
    *   Notificaciones Push (Firebase).

### F. Setup en Nuevo Entorno (Oficina/Otro PC)
Si clonas el proyecto en otro equipo, la carpeta `app-movil/node_modules` y carpetas de build no estarán (por eficiencia). Para retomar el trabajo:
1.  Bajar cambios: `git pull`
2.  Entrar a carpeta: `cd app-movil`
3.  Reinstalar librerías: `npm install`
4.  Reconstruir entorno nativo: `npx cap sync`
5.  Abrir Android Studio: `npx cap open android`

---
*Bitácora Actualizada - Antigravity*
