<?php
class ReporteImpugnacionesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectCandidatos()
    {
        $sql = "SELECT id_candidato, 
                       CONCAT(nom1_candidato, ' ', nom2_candidato, ' ', ape1_candidato, ' ', ape2_candidato) as nombre
                FROM candidatos 
                WHERE estado_candidato != 0
                ORDER BY nombre ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectReporteImpugnaciones($dpto, $muni, $zona, $puesto, $porcentaje)
    {
        $wherePlaces = " WHERE p.iddpto_place = $dpto AND p.idmuni_place = $muni ";

        if ($zona != "" && $zona != "todas") {
            $wherePlaces .= " AND p.idzona_place = $zona ";
        }
        if ($puesto != "" && $puesto != "todos") {
            $wherePlaces .= " AND p.nameplace_place = '$puesto' ";
        }

        // Obtener ID Candidato Oficial
        $sqlParam = "SELECT canditado as id_candidato FROM parametros LIMIT 1";
        $requestParam = $this->select($sqlParam, array());

        $idCandidato = 0;
        if (!empty($requestParam) && !empty($requestParam['id_candidato'])) {
            $idCandidato = $requestParam['id_candidato'];
        }

        /*
            LOGICA IMPUGNACIONES:
            Mesas donde Votos Reales < (Potencial * Porcentaje / 100)
        */

        $sql = "SELECT 
                    p.mesa_place as mesa,
                    COUNT(DISTINCT p.id_place) as censo_mesa,
                    COUNT(DISTINCT e.id_elector) as mi_potencial,
                    SUM(CASE WHEN e.poll_elector = 1 THEN 1 ELSE 0 END) as mis_testigos,
                    COALESCE(SUM(br.votos_bodyresultado), 0) as votos_e14
                FROM places p
                LEFT JOIN electores e ON p.ident_place = e.ident_elector AND e.estado_elector != 0 AND e.insc_elector = 1
                LEFT JOIN headresultado hr ON hr.place_headresultado = p.id_place AND hr.estado_headresultado != 0
                LEFT JOIN bodyresultado br ON br.head_bodyresultado = hr.id_headresultado 
                                           AND br.candidato_bodyresultado = $idCandidato 
                                           AND br.estado_bodyresultado != 0
                $wherePlaces
                GROUP BY p.mesa_place
                HAVING mi_potencial > 0 AND votos_e14 < (mi_potencial * ($porcentaje / 100))
                ORDER BY CAST(p.mesa_place AS UNSIGNED) ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
