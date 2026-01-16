-- Diagnosticar Mesas Perdidas
-- Muestra qué Puestos (Nombre + Zona) existen en 'places' pero NO coincidieron con la tabla 'puestos'

SELECT DISTINCT 
    pl.nameplace_place AS 'Nombre en Places (Censo)', 
    pl.idzona_place AS 'Zona',
    COUNT(DISTINCT pl.mesa_place) as 'Cantidad Mesas Perdidas'
FROM places pl
LEFT JOIN puestos pu ON 
    TRIM(pl.nameplace_place) = TRIM(pu.nombre_puesto) 
    AND pl.idzona_place = pu.idzona_puesto
WHERE pu.id_puesto IS NULL
GROUP BY pl.nameplace_place, pl.idzona_place;
