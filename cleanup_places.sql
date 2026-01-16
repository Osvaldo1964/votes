-- LIMPIEZA FINAL DE LA TABLA PLACES
-- IMPORTANTE: Antes de ejecutar, asegúrate de que el sistema esté funcionando correctamente con la nueva estructura.

-- 1. Eliminar columnas redundantes (la informacion ahora vive en 'mesas' y 'puestos')
-- ALTER TABLE `places` DROP COLUMN `iddpto_place`;
-- ALTER TABLE `places` DROP COLUMN `idmuni_place`;
-- ALTER TABLE `places` DROP COLUMN `idzona_place`;
-- ALTER TABLE `places` DROP COLUMN `nameplace_place`;
-- ALTER TABLE `places` DROP COLUMN `mesa_place`;

-- NOTA: Se dejan comentadas por seguridad. Decomentar una por una o ejecutar el bloque si se tiene confianza plena.
-- Se recomienda primero renombrarlas (ej: `old_mesa_place`) si el motor lo permite facilmente, o simplemente borrar.

ALTER TABLE `places` DROP COLUMN `iddpto_place`, DROP COLUMN `idmuni_place`, DROP COLUMN `idzona_place`, DROP COLUMN `nameplace_place`, DROP COLUMN `mesa_place`;
