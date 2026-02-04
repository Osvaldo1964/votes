<?php
// Adjust paths based on root execution from c:\xampp\htdocs\votes
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Mysql.php';

class SchemaInspector extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function inspect($table)
    {
        $sql = "DESCRIBE $table";
        try {
            $rows = $this->select_all($sql);
            echo "\nTable: $table\n";
            echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Key", 10) . "Extra\n";
            echo str_repeat("-", 60) . "\n";
            foreach ($rows as $row) {
                echo str_pad($row['Field'], 20) . str_pad($row['Type'], 20) . str_pad($row['Key'], 10) . $row['Extra'] . "\n";
            }
        } catch (Exception $e) {
            echo "Error inspecting $table: " . $e->getMessage() . "\n";
        }
    }
}

$inspector = new SchemaInspector();
$inspector->inspect('zones');
$inspector->inspect('puestos');
$inspector->inspect('mesas');
?>