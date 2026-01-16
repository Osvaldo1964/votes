<?php

class ReporteTestigosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectReporteTestigos($dpto, $muni, $zona, $puesto)
    {
        // Filtros base sobre la tabla TESTIGOS
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
            // El filtro puesto ahora refiere al ID de la tabla PUESTOS
            $where .= " AND t.puesto_testigo = $puesto ";
        }

        $sql = "SELECT 
                    t.id_testigo,
                    CONCAT(e.nom1_elector, ' ', e.nom2_elector, ' ', e.ape1_elector, ' ', e.ape2_elector) as nombre_completo,
                    e.telefono_elector,
                    p.nombre_puesto as puesto_asignado,
                    m.name_municipality as municipio,
                    -- Subquery para traer mesas asignadas desde tabla MESAS
                    (
                        SELECT GROUP_CONCAT(me.numero_mesa ORDER BY CAST(me.numero_mesa AS UNSIGNED) SEPARATOR ', ')
                        FROM mesas me
                        WHERE me.id_testigo_mesa = t.id_testigo
                    ) as mesas_asignadas
                FROM testigos t
                INNER JOIN electores e ON t.elector_testigo = e.id_elector
                INNER JOIN puestos p ON t.puesto_testigo = p.id_puesto
                INNER JOIN municipalities m ON t.muni_testigo = m.id_municipality
                $where
                ORDER BY p.nombre_puesto, nombre_completo ASC";

        $request = $this->select_all($sql);
        return $request;
    }

    public function selectMesasSinAsignar($dpto, $muni, $zona, $puesto)
    {
        // 1. Base WHERE: Mesas libres y Puestos activos
        // Unimos mesas con puestos para filtrar por ubicacion
        $where = " WHERE (m.id_testigo_mesa IS NULL OR m.id_testigo_mesa = 0) ";

        // 2. Construcción dinámica de filtros correpondiente a PUESTOS
        if ($puesto != "" && $puesto != "todos") {
            $where .= " AND p.id_puesto = $puesto ";
        } else {
            if ($zona != "" && $zona != "todas") {
                $where .= " AND p.idzona_puesto = $zona ";
            }
            // Para Muni y Dpto necesitamos ir a Zones -> Munis -> Depts 
            // O confiar en que el usuario filtró en cascada correcto.
            // Dado que el puesto ya tiene zona, y zona tiene el resto via joins:
            if ($muni != "") {
                $where .= " AND z.muni_zone = $muni ";
                // Ajuste: idmuni_place NO EXISTE en puestos. 
                // Puestos -> Zones -> (muni_zone)
            }
        }

        $sql = "SELECT 
                    'MESAS SIN ASIGNAR' as nombre_completo,
                    '---' as telefono_elector,
                    p.nombre_puesto as puesto_asignado,
                    GROUP_CONCAT(m.numero_mesa ORDER BY CAST(m.numero_mesa AS UNSIGNED) SEPARATOR ', ') as mesas_asignadas
                FROM mesas m
                INNER JOIN puestos p ON m.id_puesto_mesa = p.id_puesto
                INNER JOIN zones z ON p.idzona_puesto = z.id_zone
                $where
                GROUP BY p.id_puesto
                ORDER BY p.nombre_puesto ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
