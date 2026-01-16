-- ACTUALIZACION FINAL: BodyResultado
-- Este script adapta la tabla bodyresultado para trabajar con la nueva tabla 'mesas'.

-- 1. Agregar columna id_mesa_body
ALTER TABLE `bodyresultado` ADD COLUMN `id_mesa_body` bigint(20) DEFAULT NULL;

-- 2. Crear Indice y FK
ALTER TABLE `bodyresultado` ADD INDEX (`id_mesa_body`);
ALTER TABLE `bodyresultado` ADD CONSTRAINT `fk_body_mesa` FOREIGN KEY (`id_mesa_body`) REFERENCES `mesas` (`id_mesa`) ON DELETE CASCADE;

-- 3. (Opcional) Migrar datos antiguos si se requiere
-- Si tenias datos en headresultado, podrias intentar moverlos, pero asumiremos reinicio de pruebas.

-- 4. Opcional: Eliminar la dependencia antigua (Solo si estas seguro de borrar historial de pruebas)
-- ALTER TABLE `bodyresultado` DROP FOREIGN KEY ...; 
-- ALTER TABLE `bodyresultado` DROP COLUMN `head_bodyresultado`;
