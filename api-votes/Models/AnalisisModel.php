<?php
class AnalisisModel extends Mysql
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

    public function selectReporteAnalisis($dpto, $muni, $zona, $puesto, $idCandidato)
    {
        $wherePlaces = " WHERE p.iddpto_place = $dpto AND p.idmuni_place = $muni ";

        if ($zona != "" && $zona != "todas") {
            $wherePlaces .= " AND p.idzona_place = $zona ";
        }
        if ($puesto != "" && $puesto != "todos") {
            $wherePlaces .= " AND p.nameplace_place = '$puesto' ";
        }

        /*
            LOGICA ANALISIS (AUDITORIA E-14):
            Col 1: Mesa
            Col 2: Censo Mesa (COUNT places)
            Col 3: Mi Potencial (COUNT electores)
            Col 4: Mis Testigos (COUNT poll=1)
            Col 5: E-14 (SUM bodyresultado.votos) para el candidato seleccionado
        */

        $sql = "SELECT 
                    p.mesa_place as mesa,
                    COUNT(DISTINCT p.id_place) as censo_mesa,
                    COUNT(DISTINCT e.id_elector) as mi_potencial,
                    SUM(CASE WHEN e.poll_elector = 1 THEN 1 ELSE 0 END) as mis_testigos,
                    COALESCE(SUM(br.votos_bodyresultado), 0) as votos_e14
                FROM places p
                -- Join Electores (Left, porque puede haber mesas sin mis electores)
                LEFT JOIN electores e ON p.ident_place = e.ident_elector AND e.estado_elector != 0
                -- Join Resultados (Left, porque puede no haber E-14 cargado aun)
                LEFT JOIN headresultado hr ON hr.place_headresultado = p.id_place AND hr.estado_headresultado != 0
                LEFT JOIN bodyresultado br ON br.head_bodyresultado = hr.id_headresultado 
                                           AND br.candidato_bodyresultado = $idCandidato 
                                           AND br.estado_bodyresultado != 0
                $wherePlaces
                GROUP BY p.mesa_place
                ORDER BY CAST(p.mesa_place AS UNSIGNED) ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
