-- 1. Crear tabla Mesas con Columnas de Resultados (Reemplaza headresultado)
CREATE TABLE IF NOT EXISTS `mesas` (
  `id_mesa` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_puesto_mesa` bigint(20) NOT NULL,
  `numero_mesa` varchar(10) NOT NULL,
  
  -- Campos de Auditoría / Resultados (Ex-HeadResultado)
  `formulario_mesa` varchar(50) DEFAULT NULL,
  `usuario_mesa` bigint(20) DEFAULT NULL,
  `fecha_mesa` datetime DEFAULT NULL,
  `estado_mesa` int(1) NOT NULL DEFAULT 1 COMMENT '1: Activa/Pendiente, 2: Informada',

  -- Testigo asignado
  `id_testigo_mesa` bigint(20) DEFAULT NULL,

  PRIMARY KEY (`id_mesa`),
  UNIQUE KEY `unique_mesa_puesto` (`id_puesto_mesa`, `numero_mesa`),
  KEY `id_puesto_mesa` (`id_puesto_mesa`),
  KEY `id_testigo_mesa` (`id_testigo_mesa`),
  CONSTRAINT `fk_mesas_puestos` FOREIGN KEY (`id_puesto_mesa`) REFERENCES `puestos` (`id_puesto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Migrar Mesas desde Places
INSERT INTO `mesas` (numero_mesa, id_puesto_mesa)
SELECT DISTINCT 
    pl.mesa_place, 
    pu.id_puesto
FROM `places` pl
INNER JOIN `puestos` pu ON 
    TRIM(pl.nameplace_place) = TRIM(pu.nombre_puesto) AND 
    pl.idzona_place = pu.idzona_puesto
ORDER BY pu.idzona_puesto, pu.num_puesto, CAST(pl.mesa_place AS UNSIGNED);

-- 3. Vincular Censo (Places) a Mesas
ALTER TABLE `places` ADD COLUMN `id_mesa_new` bigint(20);
ALTER TABLE `places` ADD INDEX (`id_mesa_new`);

UPDATE `places` p
INNER JOIN `puestos` pu ON TRIM(p.nameplace_place) = TRIM(pu.nombre_puesto) AND p.idzona_place = pu.idzona_puesto
INNER JOIN `mesas` m ON m.id_puesto_mesa = pu.id_puesto AND m.numero_mesa = p.mesa_place
SET p.id_mesa_new = m.id_mesa;

-- 4. Actualizar BodyResultado (Si se va a conservar data histórica o preparar para nueva)
-- ALTER TABLE `bodyresultado` ADD COLUMN `id_mesa_body` bigint(20);
-- DROP TABLE `headresultado`; -- Ejecutar solo si estamos seguros de borrar la historia antigua
