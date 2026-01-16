<?php

class ReporteTestigosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectReporteTestigos($dpto, $muni, $zona, $puesto)
    {
        $where = " WHERE t.estado_testigo != 0 ";

        if ($dpto != "") {
            $where .= " AND t.dpto_testigo = $dpto ";
        }
        if ($muni != "") {
            $where .= " AND t.muni_testigo = $muni ";
        }
        if ($zona != "" && $zona != "todas") {
            $where .= " AND t.zona_testigo = $zona ";
        }
        if ($puesto != "" && $puesto != "todos") {
            // Asumiendo que el filtro puesto envía el nombre o ID. 
            // Si es ID (match con api/Functions_analisis.js que envia ID de Places si selectTestigos se basa en ID)
            // Revisando TestigosModel, puesto_testigo es bigint (ID).
            // Pero en AnalisisModel/View filtro puesto envia NOMBRE. 
            // VOY A ASUMIR QUE EL FILTRO ENVIARA ID (CORRECTO PARA REPORTES EXACTOS).
            // Si el front envia nombre, ajustare luego. Por ahora asumo ID place.
            $where .= " AND t.puesto_testigo = $puesto ";
        }

        $sql = "SELECT 
                    t.id_testigo,
                    CONCAT(e.nom1_elector, ' ', e.nom2_elector, ' ', e.ape1_elector, ' ', e.ape2_elector) as nombre_completo,
                    e.telefono_elector,
                    p.nameplace_place as puesto_asignado,
                    m.name_municipality as municipio,
                    -- Subquery o Group Concat para mesas
                    (
                        SELECT GROUP_CONCAT(p_mesa.mesa_place ORDER BY CAST(p_mesa.mesa_place AS UNSIGNED) SEPARATOR ', ')
                        FROM headresultado hr
                        INNER JOIN places p_mesa ON hr.place_headresultado = p_mesa.id_place
                        WHERE hr.testigo_headresultado = t.id_testigo
                    ) as mesas_asignadas
                FROM testigos t
                INNER JOIN electores e ON t.elector_testigo = e.id_elector
                INNER JOIN places p ON t.puesto_testigo = p.id_place
                INNER JOIN municipalities m ON t.muni_testigo = m.id_municipality
                $where
                ORDER BY p.nameplace_place, nombre_completo ASC";

        $request = $this->select_all($sql);
        return $request;
    }
    public function selectMesasSinAsignar($dpto, $muni, $zona, $puesto)
    {
        // 1. Base WHERE: Mesas libres
        $where = " WHERE (hr.testigo_headresultado IS NULL OR hr.testigo_headresultado = 0) ";

        // 2. Construcción dinámica de filtros optimizada
        if ($puesto != "" && $puesto != "todos") {
            // CORRECCIÓN CRÍTICA:
            // El $puesto es un id_place de UNA sola mesa. Si filtramos por ese ID, solo revisamos esa mesa.
            // Queremos revisar TODAS las mesas de ese PUESTO FÍSICO.
            // Solución: Filtrar por las mesas que tengan el MISMO NOMBRE y ZONA que la mesa/puesto seleccionado.

            // Subconsulta para igualar nombre y zona
            $where .= " AND p.nameplace_place = (SELECT nameplace_place FROM places WHERE id_place = $puesto) ";
            $where .= " AND p.idzona_place = (SELECT idzona_place FROM places WHERE id_place = $puesto) ";
            $where .= " AND p.idmuni_place = (SELECT idmuni_place FROM places WHERE id_place = $puesto) ";
        } else {
            // Si NO hay puesto específico, validamos por jerarquía
            if ($zona != "" && $zona != "todas") {
                $where .= " AND p.idzona_place = $zona ";
            }

            if ($muni != "") {
                $where .= " AND p.idmuni_place = $muni ";
            } else if ($dpto != "") {
                $where .= " AND p.iddpto_place = $dpto ";
            }
        }

        $sql = "SELECT 
                    'MESAS SIN ASIGNAR' as nombre_completo,
                    '---' as telefono_elector,
                    p.nameplace_place as puesto_asignado,
                    GROUP_CONCAT(p.mesa_place ORDER BY CAST(p.mesa_place AS UNSIGNED) SEPARATOR ', ') as mesas_asignadas
                FROM headresultado hr
                INNER JOIN places p ON hr.place_headresultado = p.id_place
                $where
                GROUP BY p.nameplace_place, p.idzona_place, p.idmuni_place
                ORDER BY p.nameplace_place ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
