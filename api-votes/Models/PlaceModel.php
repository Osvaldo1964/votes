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
        $sql = "SELECT d.name_department,m.name_municipality,z.name_zone,p.nameplace_place,p.ape1_place,p.ape2_place,p.nom1_place,
                            p.nom2_place,p.mesa_place
                    FROM places p
                    INNER JOIN departments d ON p.iddpto_place = d.id_department
                    INNER JOIN municipalities m ON p.idmuni_place = m.id_municipality
                    INNER JOIN zones z ON p.idzona_place = z.id_zone
                    WHERE p.ident_place = ?";
        $arrData = array($this->intIdPlace);
        $request = $this->select($sql, $arrData);
        return $request;
    }
}
