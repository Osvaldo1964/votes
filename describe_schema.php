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
        print_r($this->select_all("DESCRIBE electores"));
        print_r($this->select_all("DESCRIBE lideres"));
    }
}
$d = new Describer();
$d->describe();
?>