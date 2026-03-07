# Documentación Módulo de Reportes e Impresión

## 1. Visión General
El sistema de reportes ha sido actualizado para proporcionar herramientas de exportación y visualización offline, permitiendo a los coordinadores manejar listas físicas o trabajar en herramientas externas como Excel.

## 2. Funcionalidades de Exportación
Implementadas en el "Informe de Electores" (`app-votes/Views/Infelectores/infelectores.php`):

### Impresión Optimizada
*   **Botón Imprimir**: Genera una vista limpia del reporte.
*   **Lógica `noprint`**: Se utiliza CSS especializado para ocultar los filtros de búsqueda y botones de acción durante la impresión, dejando solo los datos tabulares y encabezados.
*   **Estilos**: Mantiene el formato de cuadrícula (border-table) de Bootstrap para una lectura clara en papel.

### Exportación a Excel
*   **Botón Excel**: Implementación client-side que convierte las tablas HTML directamente a formato `.xls`.
*   **Ventajas**: No requiere librerías pesadas en el servidor (como PhpSpreadsheet), lo que garantiza rapidez y bajo consumo de recursos.

## 3. Reportes Clave

### Informe de Electores
Generado dinámicamente según el Líder seleccionado.
- **Datos incluidos**: Cédula, Nombre, Teléfono, Dirección, Dpto, Muni, Zona, Puesto, Mesa e Inscrito.
- **Join de Ubicación**: Realiza cruce con la tabla `mesas` para mostrar el número real de la mesa de votación.

### Reporte Electoral - Potencial vs Realidad
Módulo comparativo que cruza el censo total (`places`) contra los electores registrados (`electores`).
- **Integridad**: Actualizado para contar a todos los electores registrados (independientemente del flag `insc_elector`) para asegurar que el 100% de la gestión sea visible.

## 4. Configuración de Metas
La meta global de la campaña se gestiona en `api-votes/Models/DashboardModel.php`.
- **Meta Actual**: 35,000 votos (mínimo requerido para la victoria).
- **Cálculo**: El Dashboard utiliza este valor para determinar el porcentaje de cobertura de la campaña en tiempo real.
