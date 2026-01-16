<?php

class MonitorModel extends Mysql
{
    private $intIdZona;
    private $strPuesto;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectMonitorMesa(int $idZona, string $puesto)
    {
        $this->intIdZona = $idZona;
        $this->strPuesto = $puesto;
        $puestoClean = addslashes($this->strPuesto);

        /* 
           QUERY NORMALIZADA:
           1. Base: Mesas (m) unida con Puestos (pu) para filtrar por Zona/Puesto.
           2. Potencial (Censo): COUNT(places.id_place) que apuntan a esta mesa (id_mesa_new).
           3. Mios: Electores registrados (tabla electores) que hacen match con places de esta mesa.
           4. Votaron: Electores (mios) que tienen poll_elector = 1.
        */

        $sql = "SELECT 
                    m.numero_mesa as mesa,
                    
                    /* POTENCIAL: Censo total en places para esta mesa */
                    (SELECT COUNT(*) FROM places pl WHERE pl.id_mesa_new = m.id_mesa) as potencial,

                    /* MIOS: Electores registrados que votan en esta mesa */
                    (SELECT COUNT(e.id_elector) 
                     FROM electores e 
                     INNER JOIN places pl_e ON e.ident_elector = pl_e.ident_place 
                     WHERE pl_e.id_mesa_new = m.id_mesa 
                     AND e.estado_elector != 0 
                     AND e.insc_elector = 1) as mios,

                    /* VOTARON: Electores registrados que YA votaron */
                    (SELECT COUNT(e2.id_elector)
                     FROM electores e2
                     INNER JOIN places pl_e2 ON e2.ident_elector = pl_e2.ident_place
                     WHERE pl_e2.id_mesa_new = m.id_mesa
                     AND e2.poll_elector = 1 
                     AND e2.estado_elector != 0
                     AND e2.insc_elector = 1) as votaron

                FROM mesas m
                INNER JOIN puestos pu ON m.id_puesto_mesa = pu.id_puesto
                WHERE pu.idzona_puesto = $this->intIdZona 
                AND TRIM(pu.nombre_puesto) = TRIM('$puestoClean')
                ORDER BY CAST(m.numero_mesa AS UNSIGNED) ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
