# Resumen de Refactorización: Módulo Movimientos
**Fecha:** 29 de Diciembre, 2025
**Objetivo:** Reconstruir el módulo `Movimientos` alineándolo con la arquitectura del módulo `Lideres` y corrigiendo errores de CORS, Lógica y Estructura.

## 1. Cambios Arquitectónicos
Se estandarizó la estructura del módulo siguiendo el patrón de diseño de `Lideres`:
- **Separación Estricta:** Frontend (`app-votes`) y Backend (`api-votes`) totalmente desacoplados.
- **Seguridad:** Implementación de validación de Tokens JWT (`fntAuthorization`), manejo de CORS (`OPTIONS` method), y validación de permisos de usuario (`getPermisos`).
- **JS Moderno:** Uso de `async/await` y `fetch` en lugar de `XMLHttpRequest` anidados.

## 2. Backend (API - `api-votes`)
### **Controlador (`Controllers/Movimientos.php`)**
- **CORS:** Se agregó manejo explícito del método `OPTIONS` para responder `200 OK` inmediatamente a las peticiones preflight.
- **Endpoints:**
  - `GET /getMovimientos`: Retorna lista con badges de "Ingreso/Gasto" (Badge Verde/Rojo) formateados desde el backend.
  - `GET /getMovimiento/:id`: Obtiene un registro individual.
  - `POST /setMovimiento`: Crea o actualiza registros. Recibe JSON raw.
  - `POST /delMovimiento`: "Eliminación lógica" (update estado = 0).
- **Corrección de Error 500:** Se agregaron las constantes `SPD`, `SPM`, `SMONEY` en `Config.php` y la función `formatMoney()` en `Helpers.php` que faltaban y causaban el crash del servidor.

### **Modelo (`Models/MovimientosModel.php`)**
- **Estructura de Datos:**
  - `tipo_movimiento`: Ahora almacena la **Norma Contable** (1: Campaña, 2: Otra).
  - La distinción visual **Ingreso/Gasto** NO se guarda en este campo. Se obtiene mediante un `JOIN` con la tabla `conceptos` (`conceptos.tipo_concepto`).
- **CRUD:** Métodos `selectMovimientos` (con JOINs a terceros y conceptos), `selectMovimiento`, `insertMovimiento`, `updateMovimiento`, `deleteMovimiento`.

## 3. Frontend (App - `app-votes`)
### **Vista (`Views/Movimientos/movimientos.php`)**
- **Formulario Modal:** Reordenado lógicamente:
  1. Fecha | Tercero
  2. Concepto | Norma Contable (Select `tipo_movimiento`)
  3. Observaciones
  4. Valor
- **Corrección de Carga:** Se eliminó la llamada a `getModal()` que buscaba un archivo inexistente, embebiendo el modal directamente en la vista principal para simplificar y evitar errores de ruta.

### **JavaScript (`Assets/js/functions_movimientos.js`)**
- **Cero Dependencias Externas:** El objeto de idioma `lenguajeEspanol` se define localmente para evitar bloqueos por CORS o fallos de red al cargar desde CDN.
- **Fetch Helper:** Se implementó una función `fetchData` asíncrona para manejar peticiones con Headers de Autorización (`Bearer Token`) de forma limpia.
- **Corrección de URLs:** Se ajustó la concatenación de `base_url_api` eliminando slashes duplicados que generaban errores 404/500 (e.g., de `...api//Mov...` a `...api/Mov...`).

## 4. Estado Final
- El módulo carga correctamente el DataTable.
- El formulario guarda y actualiza datos.
- Las validaciones de seguridad (Token y Permisos) están activas.
- Los errores 500 y CORS han sido erradicados.

## 5. Próximos Pasos
- Implementar **Informe de Movimientos (`Infmovimientos`)** siguiendo esta misma arquitectura (Backend Model/Controller dedicados para reportes, Frontend View/JS para filtrado y visualización).
