<?php
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";

class LugaresModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getZonas($idMuni) // Removed type hint for flexibility in debug
    {
        $sql = "SELECT DISTINCT z.id_zone, z.name_zone 
                FROM zones z 
                WHERE z.muni_zone = $idMuni 
                ORDER BY z.name_zone";
        $request = $this->select_all($sql);
        return $request;
    }
}

$db = new Mysql();
// select() needs array argument even if empty
$muni = $db->select("SELECT muni_zone FROM zones LIMIT 1", array());
$idMuni = $muni['muni_zone'];

echo "Testing with Muni ID: " . $idMuni . "\n";
$model = new LugaresModel();
print_r($model->getZonas($idMuni));
?>