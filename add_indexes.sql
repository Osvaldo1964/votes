-- Optimization: Add Indexes for Monitor and General Performance

-- 1. Places: Critical for Joining with Electores
ALTER TABLE places ADD INDEX idx_ident_place (ident_place);

-- 2. Electores: Critical for Joining with Places and Counting
ALTER TABLE electores ADD INDEX idx_ident_elector (ident_elector);
ALTER TABLE electores ADD INDEX idx_poll_status (poll_elector, estado_elector, insc_elector);

-- 3. Puestos: Critical for Filtering by Zone and Name
ALTER TABLE puestos ADD INDEX idx_zona_puesto (idzona_puesto);
ALTER TABLE puestos ADD INDEX idx_nombre_puesto (nombre_puesto);
