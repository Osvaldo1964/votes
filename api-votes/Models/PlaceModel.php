<?php

class PlaceModel extends Mysql
{
    private $intIdPlace;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPlace(int $idplace)
    {
        $this->intIdPlace = $idplace;
        $sql = "SELECT d.name_department, m.name_municipality, z.name_zone, 
                       pu.nombre_puesto as nameplace_place, 
                       me.numero_mesa as mesa_place,
                       p.ape1_place, p.ape2_place, p.nom1_place, p.nom2_place
                FROM places p
                INNER JOIN mesas me ON p.id_mesa_new = me.id_mesa
                INNER JOIN puestos pu ON me.id_puesto_mesa = pu.id_puesto
                INNER JOIN zones z ON pu.idzona_puesto = z.id_zone
                -- Asumiendo Jerarquia Zona -> Municipio -> Departamento
                LEFT JOIN municipalities m ON z.id_municipality_zone = m.id_municipality
                LEFT JOIN departments d ON m.id_department_municipality = d.id_department
                WHERE CAST(p.ident_place AS UNSIGNED) = ?";

        $arrData = array((int) $this->intIdPlace);
        $request = $this->select($sql, $arrData);
        return $request;
    }
}
