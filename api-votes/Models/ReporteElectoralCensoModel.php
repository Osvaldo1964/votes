<?php

class ReporteElectoralCensoModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener Zonas por Municipio (Correcto en Zones)
    public function selectZonas(int $idMuni)
    {
        $sql = "SELECT id_zone, name_zone FROM zones WHERE muni_zone = $idMuni";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener Puestos por Zona (Desde tabla puestos)
    public function selectPuestos(int $idZona)
    {
        // Ahora consultamos la tabla PUESTOS
        $sql = "SELECT DISTINCT nombre_puesto as puesto 
                FROM puestos 
                WHERE idzona_puesto = $idZona 
                ORDER BY nombre_puesto ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener Mesas por Puesto y Zona
    public function selectMesas(int $idZona, string $nombrePuesto)
    {
        // 1. Encontrar ID del puesto
        $nombrePuesto = strClean($nombrePuesto);
        $sqlPuesto = "SELECT id_puesto FROM puestos WHERE idzona_puesto = $idZona AND nombre_puesto = '$nombrePuesto'";
        $puesto = $this->select($sqlPuesto, array());

        if (empty($puesto))
            return [];
        $idPuesto = $puesto['id_puesto'];

        // 2. Traer mesas de la tabla MESAS
        $sql = "SELECT numero_mesa as mesa 
                FROM mesas
                WHERE id_puesto_mesa = $idPuesto
                ORDER BY CAST(numero_mesa AS UNSIGNED) ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    // Generar el Reporte Consolidado
    public function selectReporteCenso($dpto, $muni, $zona, $puesto, $mesa, $tipoReporte = 'detallado')
    {
        // BASE: Places (Censo) -> Mesas -> Puestos -> Zonas

        $where = " WHERE 1=1 ";

        if ($zona != "") {
            $where .= " AND z.id_zone = $zona ";
        } else {
            // Si no hay zona especifica, filtrar por muni (en zones)
            if ($muni != "") {
                $where .= " AND z.muni_zone = $muni ";
            }
        }

        if ($puesto != "") {
            $where .= " AND pu.nombre_puesto = '$puesto' ";
        }

        if ($mesa != "" && $mesa != "todos") {
            $where .= " AND m.numero_mesa = '$mesa' ";
        }

        // --- DEFINICIÓN DE AGRUPACIÓN Y COLUMNAS ---

        // Valores por defecto (Detallado / Mesa)
        $selectCols = "z.name_zone, pu.nombre_puesto as puesto, m.numero_mesa as mesa";
        $groupBy = "z.name_zone, pu.nombre_puesto, m.numero_mesa";
        $orderBy = "z.name_zone, pu.nombre_puesto, CAST(m.numero_mesa AS UNSIGNED)";

        if ($tipoReporte === 'resumido') {
            // Lógica de Agrupación Inteligente

            // Si NO se ha seleccionado una Mesa específica (mesa vacía o "todos")
            if (empty($mesa) || $mesa === "todos") {

                // Si NO se ha seleccionado un Puesto específico
                if (empty($puesto) || $puesto === "todos") {

                    // Si se seleccionó una ZONA -> Agrupar por PUESTOS
                    if (!empty($zona) && $zona !== "todas") {
                        $selectCols = "z.name_zone, pu.nombre_puesto as puesto, 'VARIAS' as mesa";
                        $groupBy = "z.name_zone, pu.nombre_puesto";
                        $orderBy = "z.name_zone, pu.nombre_puesto";
                    }
                    // Si NO se seleccionó Zona (solo Muni) -> Agrupar por ZONAS
                    else {
                        $selectCols = "z.name_zone, 'VARIOS' as puesto, 'VARIAS' as mesa";
                        $groupBy = "z.name_zone";
                        $orderBy = "z.name_zone";
                    }
                } else {
                    // Si se seleccionó PUESTO -> Mostrar TOTAL del Puesto (agrupar mesas)
                    $selectCols = "z.name_zone, pu.nombre_puesto as puesto, 'TODAS' as mesa";
                    $groupBy = "z.name_zone, pu.nombre_puesto";
                    $orderBy = "z.name_zone, pu.nombre_puesto";
                }
            }
        }

        /*
           QUERY JOIN:
           Places p
           INNER JOIN Mesas m ON p.id_mesa_new = m.id_mesa
           INNER JOIN Puestos pu ON m.id_puesto_mesa = pu.id_puesto
           INNER JOIN Zones z ON pu.idzona_puesto = z.id_zone
           LEFT JOIN Electores e ON p.ident_place = e.ident_elector
        */

        $sql = "SELECT 
                    $selectCols,
                    COUNT(p.id_place) as potencial,
                    COUNT(IF(e.estado_elector != 0 AND e.insc_elector = 1, 1, NULL)) as mis_votos
                FROM places p
                INNER JOIN mesas m ON p.id_mesa_new = m.id_mesa
                INNER JOIN puestos pu ON m.id_puesto_mesa = pu.id_puesto
                INNER JOIN zones z ON pu.idzona_puesto = z.id_zone
                LEFT JOIN electores e ON p.ident_place = e.ident_elector 
                $where
                GROUP BY $groupBy
                ORDER BY $orderBy";

        $request = $this->select_all($sql);
        return $request;
    }
}
