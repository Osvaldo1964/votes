<?php
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";

class Optimizer extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    public function optimize()
    {
        $queries = [
            "ALTER TABLE places ADD INDEX idx_ident_place (ident_place)",
            "ALTER TABLE electores ADD INDEX idx_ident_elector (ident_elector)",
            "ALTER TABLE electores ADD INDEX idx_poll_status (poll_elector, estado_elector, insc_elector)",
            "ALTER TABLE puestos ADD INDEX idx_zona_puesto (idzona_puesto)",
            "ALTER TABLE puestos ADD INDEX idx_nombre_puesto (nombre_puesto)"
        ];

        foreach ($queries as $sql) {
            echo "Executing: $sql ... ";
            try {
                // Using a direct query execution if possible, or select logic.
                // Mysql class usually has update/insert/select. ALTER is like update (no return).
                // Let's assume update() or insert() works for generic queries, or raw connection.
                // The Mysql class in this project usually uses prepare/execute.

                // Let's try update() as it doesn't expect return data.
                $this->update($sql, array());
                echo "OK\n";
            } catch (Exception $e) {
                // If index exists, it might throw.
                echo "Error (maybe exists): " . $e->getMessage() . "\n";
            }
        }
    }
}

$opt = new Optimizer();
$opt->optimize();
?>