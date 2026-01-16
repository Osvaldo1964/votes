-- 1. Crear tabla Mesas
CREATE TABLE IF NOT EXISTS `mesas` (
  `id_mesa` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_puesto_mesa` bigint(20) NOT NULL,
  `numero_mesa` varchar(10) NOT NULL,
  `estado_mesa` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_mesa`),
  UNIQUE KEY `unique_mesa_puesto` (`id_puesto_mesa`, `numero_mesa`),
  KEY `id_puesto_mesa` (`id_puesto_mesa`),
  CONSTRAINT `fk_mesas_puestos` FOREIGN KEY (`id_puesto_mesa`) REFERENCES `puestos` (`id_puesto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Migrar Mesas desde Places
-- Emparejamos por nombre de puesto y zona para encontrar el ID del puesto correcto
INSERT INTO `mesas` (numero_mesa, id_puesto_mesa)
SELECT DISTINCT 
    pl.mesa_place, 
    pu.id_puesto
FROM `places` pl
INNER JOIN `puestos` pu ON 
    TRIM(pl.nameplace_place) = TRIM(pu.nombre_puesto) AND 
    pl.idzona_place = pu.idzona_puesto
ORDER BY pu.idzona_puesto, pu.num_puesto, CAST(pl.mesa_place AS UNSIGNED);
