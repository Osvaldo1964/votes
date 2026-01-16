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
        // Consultamos las zonas A TRAVES de la tabla puestos para asegurar que existen y pertenecen al municipio
        // Como Puestos (no tiene muni explicito), dependemos de la tabla Zones si tuviera muni, o de la relacion Puesto->Zona.
        // Fallback seguro: Listar zonas vinculadas a puestos activos.

        $sql = "SELECT DISTINCT z.id_zone, z.name_zone 
                FROM zones z 
                WHERE z.muni_zone = $idMuni 
                ORDER BY z.name_zone";

        // Fallback si falla SQL por columna inexistente (comentar si es necesario):
        // $sql = "SELECT DISTINCT z.id_zone, z.name_zone FROM zones z ORDER BY z.name_zone";

        $request = $this->select_all($sql);
        return $request;
    }

    public function getPuestos(int $idZona)
    {
        // Consultamos la nueva tabla puestos
        $sql = "SELECT id_puesto as id_place, nombre_puesto as nameplace_place, num_puesto 
                FROM puestos 
                WHERE idzona_puesto = $idZona 
                ORDER BY nombre_puesto";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getMesas(int $idZona, string $nombrePuesto)
    {
        $nombrePuesto = strClean($nombrePuesto);

        // 1. Obtener ID del Puesto basado en nombre y zona
        $sqlPuesto = "SELECT id_puesto FROM puestos WHERE idzona_puesto = $idZona AND nombre_puesto = '$nombrePuesto'";
        $requestPuesto = $this->select($sqlPuesto, array());

        if (empty($requestPuesto))
            return array();

        $idPuesto = $requestPuesto['id_puesto'];

        // 2. Traer Mesas
        $sql = "SELECT id_mesa, numero_mesa as nombre_mesa
                FROM mesas 
                WHERE id_puesto_mesa = $idPuesto 
                ORDER BY CAST(numero_mesa AS UNSIGNED)";

        $request = $this->select_all($sql);
        return $request;
    }

    public function getPotencialMesa(int $idZona, string $nombrePuesto, string $nombreMesa)
    {
        $nombrePuesto = strClean($nombrePuesto);
        $nombreMesa = strClean($nombreMesa);

        // 1. Obtener ID Mesa
        $sqlMesa = "SELECT m.id_mesa FROM mesas m
                    INNER JOIN puestos p ON m.id_puesto_mesa = p.id_puesto
                    WHERE p.idzona_puesto = $idZona 
                    AND p.nombre_puesto = '$nombrePuesto'
                    AND m.numero_mesa = '$nombreMesa'";
        $requestMesa = $this->select($sqlMesa, array());

        if (empty($requestMesa))
            return array('total' => 0);
        $idMesa = $requestMesa['id_mesa'];

        // 2. Contar en places usando id_mesa_new
        $sql = "SELECT COUNT(*) as total FROM places WHERE id_mesa_new = $idMesa";
        $request = $this->select($sql, array());
        return $request;
    }
    public function getMisVotos(int $idMesa)
    {
        // Contar electores registrados (tabla electores) que votan en esta mesa (join places)
        // Se asume match por cedula (ident) ya que electores no tiene id_mesa directo
        $sql = "SELECT COUNT(e.id_elector) as total
                FROM electores e
                INNER JOIN places p ON e.ident_elector = p.ident_place
                WHERE p.id_mesa_new = $idMesa AND e.estado_elector != 0";
        $request = $this->select($sql, array());
        return $request;
    }
}
