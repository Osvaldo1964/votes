# Documentación Módulo Electores 2026

## 1. Visión General
El módulo de Electores ha sido transformado para actuar no solo como un registro de intención de voto, sino como un gestor dinámico del censo electoral local. El sistema ahora permite capturar la ubicación exacta de votación (Departamento, Municipio, Zona, Puesto y Mesa) de forma normalizada, incluso para ciudadanos que no figuran en el padrón inicial cargado en la tabla `places`.

## 2. Arquitectura de Datos
El flujo de datos se apoya en 5 tablas principales de infraestructura:
*   `departments`: Departamentos nacionales.
*   `municipalities`: Municipios vinculados a departamentos.
*   `zones`: Divisiones zonales (Comunas) dentro de un municipio.
*   `puestos`: Puestos de votación físicos vinculados a una zona.
*   `mesas`: Mesas de votación vinculadas a un puesto.

### Sincronización con `places` (Censo)
Cuando se registra un elector, el sistema ejecuta una lógica de **"Sincronización Automática"**:
1.  Busca la cédula en `places`.
2.  Si **no existe**: Crea un nuevo registro en `places` con los nombres y el `id_mesa_new` seleccionado.
3.  Si **existe**: Actualiza el registro en `places` con la ubicación y nombres capturados.

Esto garantiza que la tabla `places` sea un "ser vivo" que crece orgánicamente con la gestión de los líderes.

## 3. Componentes Frontend (Vanilla JS)
Archivo: `app-votes/Assets/js/functions_electores.js`

### Cascada Geográfica
Se implementó un motor de cascada dinámico usando los helpers de la API:
*   `fntGetDepartamentos()`
*   `fntGetMunicipios(idDpto)`
*   `fntGetZonas(idMuni)`
*   `fntGetPuestos(idZona)`
*   `fntGetMesas(nombrePuesto)`

### Búsqueda Inteligente (Evento Blur)
Al salir del campo "Cédula", el sistema dispara una petición a `/Electores/getValidaElector/`. Si el ciudadano está en el censo, la función `setUbicacionCascada(ids)` se encarga de:
1.  Pre-seleccionar el departamento y cargar sus municipios.
2.  Pre-seleccionar el municipio y cargar sus zonas.
3.  ...y así sucesivamente hasta la mesa.

## 4. Backend (PHP MVC)
Archivo: `api-votes/Controllers/Electores.php` y `api-votes/Models/ElectoresModel.php`

### Endpoints Clave
*   `getValidaElector($cedula)`: Devuelve no solo si existe, sino el árbol completo de IDs geográficos para que el frontend pueda reconstruir la cascada.
*   `insertOrUpdatePlace(...)`: Método crítico que mantiene la integridad entre la tabla de gestión (`electores`) y la tabla de censo (`places`).
*   `getUbicacionMesa($idMesa)`: Helper que permite reconstruir la ruta geográfica de un elector basándose únicamente en su ID de mesa (utilizado en el modo edición).

## 5. Escalabilidad
Este módulo ya está preparado para ser **Nacional**. Al no depender de archivos JSON estáticos para la ubicación, sino de consultas dinámicas a las tablas maestras, puede manejar cualquier cantidad de departamentos y municipios sin degradar el rendimiento o la experiencia de usuario.
