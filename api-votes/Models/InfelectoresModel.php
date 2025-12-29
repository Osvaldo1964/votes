<?php
class InfelectoresModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectLideres()
    {
        $sql = "SELECT id_lider, CONCAT(nom1_lider, ' ', nom2_lider, ' ', ape1_lider, ' ', ape2_lider) as nombre_lider 
                FROM lideres 
                WHERE estado_lider != 0 
                ORDER BY nombre_lider ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectElectoresReport($lider)
    {
        if ($lider == 'todos') {
            $sql = "SELECT e.id_elector, e.ident_elector, 
                           CONCAT(e.nom1_elector, ' ', e.nom2_elector, ' ', e.ape1_elector, ' ', e.ape2_elector) as nombre_elector,
                           e.telefono_elector, e.email_elector, e.direccion_elector,
                           l.id_lider, CONCAT(l.nom1_lider, ' ', l.nom2_lider, ' ', l.ape1_lider, ' ', l.ape2_lider) as nombre_lider
                    FROM electores e
                    INNER JOIN lideres l ON e.lider_elector = l.id_lider
                    WHERE e.estado_elector != 0
                    ORDER BY nombre_lider ASC, nombre_elector ASC";
            $request = $this->select_all($sql);
        } else {
            $sql = "SELECT e.id_elector, e.ident_elector, 
                           CONCAT(e.nom1_elector, ' ', e.nom2_elector, ' ', e.ape1_elector, ' ', e.ape2_elector) as nombre_elector,
                           e.telefono_elector, e.email_elector, e.direccion_elector,
                           l.id_lider, CONCAT(l.nom1_lider, ' ', l.nom2_lider, ' ', l.ape1_lider, ' ', l.ape2_lider) as nombre_lider
                    FROM electores e
                    INNER JOIN lideres l ON e.lider_elector = l.id_lider
                    WHERE e.lider_elector = $lider AND e.estado_elector != 0
                    ORDER BY nombre_elector ASC";
            $request = $this->select_all($sql);
        }
        return $request;
    }
}
