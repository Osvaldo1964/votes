<?php
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";

class Describer extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    public function describe()
    {
        $sql = "DESCRIBE municipalities";
        $res = $this->select_all($sql);
        print_r($res);
    }
}
$d = new Describer();
$d->describe();
?>