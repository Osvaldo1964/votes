-- 1. Insertar Puestos Faltantes
INSERT INTO `puestos` (`idzona_puesto`, `num_puesto`, `nombre_puesto`) VALUES
(9, '01', 'CALABAZO'), -- Asumo num 01 si no existe, o increméntalo
(9, '04', 'EL CAMPANO'),
(9, '05', 'LA TAGUA'),
(9, '06', 'MINCA'),
(9, '07', 'TAGANGA');

-- 2. Corregir/Insertar Universidad Antonio Nariño (Zona 3)
-- El problema es la Ñ mal codificada en places (NARIO).
-- Insertamos el nombre CORRECTO en puestos (ya estaba insertado como 'UNIVERSIDAD ANTONIO NARIÑO' en mi script, 
-- pero el de places tiene 'NARIO').
-- ESTRATEGIA: Actualizar `places` para que coincida con `puestos`.
UPDATE places SET nameplace_place = 'UNIVERSIDAD ANTONIO NARIÑO' 
WHERE nameplace_place LIKE 'UNIVERSIDAD ANTONIO NARI%' AND idzona_place = 3;

-- 3. Reintentar Migración de Mesas Faltantes
-- (Insertamos solo las que NO estén ya en mesas, usando IGNORE o LEFT JOIN)

INSERT INTO `mesas` (numero_mesa, id_puesto_mesa, estado_mesa)
SELECT DISTINCT 
    pl.mesa_place, 
    pu.id_puesto,
    1
FROM `places` pl
INNER JOIN `puestos` pu ON 
    TRIM(pl.nameplace_place) = TRIM(pu.nombre_puesto) AND 
    pl.idzona_place = pu.idzona_puesto
LEFT JOIN `mesas` m_exist ON m_exist.id_puesto_mesa = pu.id_puesto AND m_exist.numero_mesa = pl.mesa_place
WHERE m_exist.id_mesa IS NULL; -- Solo las que faltan

-- 4. Reintentar Vinculación de Censo (Solo para los NULL)
UPDATE `places` p
INNER JOIN `puestos` pu ON TRIM(p.nameplace_place) = TRIM(pu.nombre_puesto) AND p.idzona_place = pu.idzona_puesto
INNER JOIN `mesas` m ON m.id_puesto_mesa = pu.id_puesto AND m.numero_mesa = p.mesa_place
SET p.id_mesa_new = m.id_mesa
WHERE p.id_mesa_new IS NULL;
