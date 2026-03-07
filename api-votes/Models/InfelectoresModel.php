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
        $where = "WHERE e.estado_elector != 0";
        if ($lider != 'todos') {
            $where .= " AND e.lider_elector = $lider";
        }

        $sql = "SELECT e.id_elector, e.ident_elector, 
                       CONCAT(e.nom1_elector, ' ', e.nom2_elector, ' ', e.ape1_elector, ' ', e.ape2_elector) as nombre_elector,
                       e.telefono_elector, e.email_elector, e.direccion_elector, e.insc_elector,
                       l.id_lider, CONCAT(l.nom1_lider, ' ', l.nom2_lider, ' ', l.ape1_lider, ' ', l.ape2_lider) as nombre_lider,
                       d.name_department as dpto, m.name_municipality as muni, z.name_zone as zona, p.nombre_puesto as puesto, me.numero_mesa as mesa
                FROM electores e
                INNER JOIN lideres l ON e.lider_elector = l.id_lider
                LEFT JOIN places pl ON e.ident_elector = pl.ident_place
                LEFT JOIN mesas me ON pl.id_mesa_new = me.id_mesa
                LEFT JOIN puestos p ON me.id_puesto_mesa = p.id_puesto
                LEFT JOIN zones z ON p.idzona_puesto = z.id_zone
                LEFT JOIN municipalities m ON z.muni_zone = m.id_municipality
                LEFT JOIN departments d ON m.id_department_municipality = d.id_department
                $where
                ORDER BY nombre_lider ASC, nombre_elector ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
