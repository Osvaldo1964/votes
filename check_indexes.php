<?php
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";

class IndexChecker extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    public function check()
    {
        $tables = ['places', 'electores', 'mesas', 'puestos'];
        foreach ($tables as $t) {
            echo "--- Indexes for table: $t ---\n";
            $indexes = $this->select_all("SHOW INDEX FROM $t");
            foreach ($indexes as $idx) {
                echo "Key: " . $idx['Key_name'] . " | Column: " . $idx['Column_name'] . "\n";
            }
            echo "\n";
        }
    }
}
$c = new IndexChecker();
$c->check();
?>