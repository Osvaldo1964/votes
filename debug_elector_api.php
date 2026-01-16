<?php
// Adjust paths to root
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";

// Mock the model
class ElectoresModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    public function selectPlace(string $id_elector)
    {
        // FIXED SQL
        $sql = "SELECT d.name_department, m.name_municipality, z.name_zone, 
                       pu.nombre_puesto as nameplace_place, 
                       me.numero_mesa as mesa_place,
                       p.ape1_place, p.ape2_place, p.nom1_place, p.nom2_place, p.ident_place 
                FROM places p
                INNER JOIN mesas me ON p.id_mesa_new = me.id_mesa
                INNER JOIN puestos pu ON me.id_puesto_mesa = pu.id_puesto
                INNER JOIN zones z ON pu.idzona_puesto = z.id_zone
                LEFT JOIN municipalities m ON z.muni_zone = m.id_municipality
                LEFT JOIN departments d ON m.id_department_municipality = d.id_department
                WHERE CAST(p.ident_place AS UNSIGNED) = ?";

        $arrData = array((int) $id_elector);
        $request = $this->select($sql, $arrData);
        return $request;
    }
}

$model = new ElectoresModel();
$res = $model->selectPlace("73111404");
print_r($res);
?>