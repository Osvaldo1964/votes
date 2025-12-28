<?php

class LugaresModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDepartamentos()
    {
        $sql = "SELECT id_department, name_department FROM departments ORDER BY name_department";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getMunicipios(int $idDpto)
    {
        $sql = "SELECT id_municipality, name_municipality FROM municipalities WHERE id_department_municipality = $idDpto ORDER BY name_municipality";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getZonas(int $idMuni)
    {
        // Asumiendo que las zonas están relacionadas o se obtienen de places. 
        // Si existe tabla zones, la usamos. PlaceModel hacia join con zones z.
        $sql = "SELECT DISTINCT z.id_zone, z.name_zone 
                FROM zones z
                INNER JOIN places p ON p.idzona_place = z.id_zone
                WHERE p.idmuni_place = $idMuni
                ORDER BY z.name_zone";
        // Si zones tiene id_muni directo mejor, pero por seguridad uso el join con places que parece ser la tabla central
        // Revisando PlaceModel: INNER JOIN zones z ON p.idzona_place = z.id_zone
        // Consulto zones directamente si es posible, pero no sé si tiene FK a municipio. 
        // Haré un distinct desde places para asegurar que hay mesas ahí.

        $sql = "SELECT DISTINCT z.id_zone, z.name_zone 
                FROM zones z 
                INNER JOIN places p ON p.idzona_place = z.id_zone
                WHERE p.idmuni_place = $idMuni 
                ORDER BY z.name_zone";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getPuestos(int $idZona)
    {
        // Los puestos son el 'nameplace_place' en la tabla places, agrupados.
        // Se asume que 'places' es la tabla de MESAS.
        $sql = "SELECT DISTINCT nameplace_place 
                FROM places 
                WHERE idzona_place = $idZona 
                ORDER BY nameplace_place";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getMesas(int $idZona, string $nombrePuesto)
    {
        // Retornamos el ID de la tabla places (que representa una mesa única) y el número de mesa
        $nombrePuesto =  strClean($nombrePuesto); // Sanear
        // Nota: nameplace_place es string.
        $sql = "SELECT MAX(id_place) as id_mesa, mesa_place as nombre_mesa
                FROM places 
                WHERE idzona_place = $idZona AND nameplace_place = '$nombrePuesto' 
                GROUP BY mesa_place
                ORDER BY CAST(mesa_place AS UNSIGNED)";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getPotencialMesa(int $idZona, string $nombrePuesto, string $nombreMesa)
    {
        $nombrePuesto = strClean($nombrePuesto);
        $nombreMesa = strClean($nombreMesa);

        // Contamos cuántos registros existen con esa combinación exacta
        $sql = "SELECT COUNT(*) as total
                FROM places 
                WHERE idzona_place = $idZona 
                AND nameplace_place = '$nombrePuesto' 
                AND mesa_place = '$nombreMesa'";
        $request = $this->select($sql, array());
        return $request;
    }
}
