# Análisis de Estructura de Datos: Geografía y Puestos de Votación

Este documento detalla la estructura y relaciones entre las tablas geográficas definidas en `Config.json` y las tablas operativas en MySQL (`zones`, `puestos`, `mesas`).

## 1. Estructura Geográfica (JSON)
La información base de la división política administrativa se encuentra en `api-votes/Json/Config.json`.

### Tablas JSON
*   **`dptos` (Departamentos)**
    *   `iddpto`: Identificador único del departamento (ej: "1").
    *   `namedpto`: Nombre del departamento (ej: "ANTIOQUIA").
    *   `codedpto`: Código DANE o interno (ej: "05").

*   **`munis` (Municipios)**
    *   `idmuni`: Identificador único del municipio (ej: "1").
    *   `namemuni`: Nombre del municipio (ej: "MEDELLÍN").
    *   `codemuni`: Código DANE (ej: "5001").
    *   `dptomuni`: Llave foránea que referencia a `dptos.iddpto`.

## 2. Estructura Operativa (MySQL)
La lógica de los puestos de votación se gestiona en la base de datos MySQL.

### Tablas MySQL
*   **`zones` (Zonas)**
    *   `id_zone`: Llave primaria.
    *   `name_zone`: Nombre de la zona.
    *   `muni_zone`: Llave foránea. Conecta con el identificador del municipio (probablemente correspondiente a `idmuni` del JSON o una tabla espejada `municipalities`).

*   **`puestos` (Puestos de Votación)**
    *   `id_puesto`: Llave primaria.
    *   `nombre_puesto`: Nombre del puesto.
    *   `num_puesto`: Número identificador del puesto.
    *   `idzona_puesto`: Llave foránea que referencia a `zones.id_zone`.

*   **`mesas` (Mesas de Votación)**
    *   `id_mesa`: Llave primaria.
    *   `numero_mesa`: Número de la mesa.
    *   `id_puesto_mesa`: Llave foránea que referencia a `puestos.id_puesto`.
    *   `formulario_mesa`: Referencia al formulario (E-14).
    *   `estado_mesa`: Estado del escrutinio (ej: 2 = Procesado).

## 3. Relación Jerárquica Completa

La jerarquía de datos fluye de la siguiente manera:

1.  **Departamento** (`dptos` en JSON / `departments` en MySQL)
    *   contiene muchos...
2.  **Municipios** (`munis` en JSON / `municipalities` en MySQL)
    *   contiene muchas...
3.  **Zonas** (`zones` en MySQL)
    *   Link: `zones.muni_zone` -> `idmuni`
    *   contiene muchos...
4.  **Puestos** (`puestos` en MySQL)
    *   Link: `puestos.idzona_puesto` -> `zones.id_zone`
    *   contiene muchas...
5.  **Mesas** (`mesas` en MySQL)
    *   Link: `mesas.id_puesto_mesa` -> `puestos.id_puesto`

### Nota Técnicas
*   Existe una tabla `municipalities` y `departments` en MySQL que parece duplicar o respaldar la información del JSON, ya que los `JOIN` en `PlaceModel.php` hacen referencia explícita a ellas.
*   `LugaresModel.php` utiliza `puestos` como punto central para validar la existencia de zonas en algunos métodos, pero la jerarquía estricta es Zona -> Puesto.
