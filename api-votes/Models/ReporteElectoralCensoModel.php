<?php

class ReporteElectoralCensoModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener Zonas por Municipio
    public function selectZonas(int $idMuni)
    {
        // Corregido: muni_zone en lugar de idmuni_zone
        $sql = "SELECT id_zone, name_zone FROM zones WHERE muni_zone = $idMuni";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener Puestos por Zona (DISTINCT de places)
    public function selectPuestos(int $idZona)
    {
        $sql = "SELECT DISTINCT nameplace_place as puesto 
                FROM places 
                WHERE idzona_place = $idZona 
                ORDER BY nameplace_place ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener Mesas por Puesto y Zona
    public function selectMesas(int $idZona, string $nombrePuesto)
    {
        $sql = "SELECT DISTINCT mesa_place as mesa 
                FROM places 
                WHERE idzona_place = $idZona AND nameplace_place = '$nombrePuesto'
                ORDER BY CAST(mesa_place AS UNSIGNED) ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    // Generar el Reporte Consolidado
    public function selectReporteCenso($dpto, $muni, $zona, $puesto, $mesa, $tipoReporte = 'detallado')
    {
        $wherePlaces = " WHERE p.iddpto_place = $dpto AND p.idmuni_place = $muni ";

        if ($zona != "") {
            $wherePlaces .= " AND p.idzona_place = $zona ";
        }
        if ($puesto != "") {
            $wherePlaces .= " AND p.nameplace_place = '$puesto' ";
        }
        if ($mesa != "" && $mesa != "todos") {
            $wherePlaces .= " AND p.mesa_place = '$mesa' ";
        }

        // --- DEFINICIÓN DE AGRUPACIÓN Y COLUMNAS ---

        // Valores por defecto (Detallado / Mesa)
        $selectCols = "z.name_zone, p.nameplace_place as puesto, p.mesa_place as mesa";
        $groupBy = "z.name_zone, p.nameplace_place, p.mesa_place";
        $orderBy = "z.name_zone, p.nameplace_place, CAST(p.mesa_place AS UNSIGNED)";

        if ($tipoReporte === 'resumido') {
            // Lógica de Agrupación Inteligente

            // Si NO se ha seleccionado una Mesa específica (mesa vacía o "todos")
            if (empty($mesa) || $mesa === "todos") {

                // Si NO se ha seleccionado un Puesto específico
                if (empty($puesto) || $puesto === "todos") {

                    // Si se seleccionó una ZONA -> Agrupar por PUESTOS
                    if (!empty($zona) && $zona !== "todas") {
                        $selectCols = "z.name_zone, p.nameplace_place as puesto, 'VARIAS' as mesa";
                        $groupBy = "z.name_zone, p.nameplace_place";
                        $orderBy = "z.name_zone, p.nameplace_place";
                    }
                    // Si NO se seleccionó Zona (solo Muni) -> Agrupar por ZONAS
                    else {
                        $selectCols = "z.name_zone, 'VARIOS' as puesto, 'VARIAS' as mesa";
                        $groupBy = "z.name_zone";
                        $orderBy = "z.name_zone";
                    }
                } else {
                    // Si se seleccionó PUESTO -> Mostrar TOTAL del Puesto (agrupar mesas)
                    $selectCols = "z.name_zone, p.nameplace_place as puesto, 'TODAS' as mesa";
                    $groupBy = "z.name_zone, p.nameplace_place";
                    $orderBy = "z.name_zone, p.nameplace_place";
                }
            }
        }

        $sql = "SELECT 
                    $selectCols,
                    COUNT(p.id_place) as potencial,
                    COUNT(e.id_elector) as mis_votos
                FROM places p
                INNER JOIN zones z ON p.idzona_place = z.id_zone
                LEFT JOIN electores e ON p.ident_place = e.ident_elector AND e.estado_elector != 0
                $wherePlaces
                GROUP BY $groupBy
                ORDER BY $orderBy";

        $request = $this->select_all($sql);
        return $request;
    }
}
