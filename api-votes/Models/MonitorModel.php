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

        // Limpiamos nombre del puesto por seguridad
        $puestoClean = addslashes($this->strPuesto);

        /* 
           QUERY COMPLEJA:
           1. Buscamos todas las mesas en 'places' para esa Zona y Puesto.
           2. Hacemos LEFT JOIN con 'electores' para ver cuántos están vinculados a esos puestos.
           3. Agrupamos por 'mesa_place'.

           COLUMNAS:
           - mesa: El número de la mesa.
           - potencial: COUNT(places.id_place) -> Total de cédulas inscritas en esa mesa (Censo).
           - mios: COUNT(electores.id) -> Total de electores registrados en mi base de datos.
           - votaron: SUM(polls) -> Total de mis electores que ya tienen poll_elector = 1.
        */

        $sql = "SELECT 
                    p.mesa_place as mesa,
                    COUNT(DISTINCT p.id_place) as potencial,
                    COUNT(DISTINCT IF(e.estado_elector != 0, e.id_elector, NULL)) as mios,
                    SUM(CASE WHEN e.poll_elector = 1 AND e.estado_elector != 0 THEN 1 ELSE 0 END) as votaron
                FROM places p
                LEFT JOIN electores e ON CAST(e.ident_elector AS CHAR) = CAST(p.ident_place AS CHAR)
                WHERE p.idzona_place = $this->intIdZona 
                AND TRIM(p.nameplace_place) = TRIM('$puestoClean')
                GROUP BY p.mesa_place
                ORDER BY CAST(p.mesa_place AS UNSIGNED) ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
