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

    public function selectReporteAnalisis($dpto, $muni, $zona, $puesto)
    {
        // Obtener el ID del Candidato Oficial desde Parámetros
        $sqlParam = "SELECT canditado as id_candidato FROM parametros LIMIT 1";
        $requestParam = $this->select($sqlParam, array());

        if (empty($requestParam) || empty($requestParam['id_candidato'])) {
            return [];
        }

        $idCandidato = $requestParam['id_candidato'];

        // FILTROS: Aplicar sobre las tablas normalizadas (Zones, Puestos)
        // La tabla principal de agrupamiento será MESAS.
        // Pero el filtro viene por jerarquía.

        $where = " WHERE 1=1 ";

        // Filtro Puesto (Si viene nombre o ID)
        if ($puesto != "" && $puesto != "todos") {
            // Asumimos nombre puesto como venia antes, OJO si el front manda ID
            // En el controller vi que recibe 'puesto'. Si es selectpicker usualmente es valor.
            // En functions_analisis.js (si existe) deberia ver que manda.
            // Por seguridad, si es numerico es ID, si es texto es nombre.
            if (is_numeric($puesto)) {
                $where .= " AND pu.id_puesto = $puesto ";
            } else {
                $where .= " AND pu.nombre_puesto = '$puesto' ";
            }
        }

        // Filtro Zona
        if ($zona != "" && $zona != "todas") {
            $where .= " AND z.id_zone = $zona ";
        } else {
            // Si no hay zona especifica, filtrar por muni/dpto
            if ($muni != "") {
                $where .= " AND z.muni_zone = $muni ";
            }
            // Dpto ya esta implicito en muni, pero por si acaso
        }

        /*
            CONSULTA NORMALIZADA:
            1. Base: Mesas (m) unida con Puestos (pu) y Zonas (z).
            2. Censo Mesa: Conteo de Places (pl) donde pl.id_mesa_new = m.id_mesa.
            3. Mi Potencial: Conteo de Electores (e) inscritos y favorables en esa mesa.
            4. Mis Testigos: Conteo de Testigos asignados a esa mesa.
               Nota: Ahora la asignación es directa en mesas.id_testigo_mesa.
               Si id_testigo_mesa > 0, hay 1 testigo.
            5. Votos E14: Suma de bodyresultado (br) vinculado a m.id_mesa (id_mesa_body).
        */

        $sql = "SELECT 
                    m.numero_mesa as mesa,
                    
                    /* CENSO: Personas en places asignadas a esta mesa */
                    (SELECT COUNT(*) FROM places pl WHERE pl.id_mesa_new = m.id_mesa) as censo_mesa,

                    /* MI POTENCIAL: Electores registrados (tabla electores) que votan en esta mesa (join places) */
                    (SELECT COUNT(e.id_elector) 
                     FROM electores e 
                     INNER JOIN places pl_e ON e.ident_elector = pl_e.ident_place 
                     WHERE pl_e.id_mesa_new = m.id_mesa 
                     AND e.estado_elector != 0 
                     AND e.insc_elector = 1) as mi_potencial,

                    /* MIS TESTIGOS: Si la mesa tiene testigo asignado (1 o 0) */
                    (CASE WHEN m.id_testigo_mesa > 0 THEN 1 ELSE 0 END) as mis_testigos,

                    /* VOTOS E14: Suma de votos del candidato parametrizado en esta mesa */
                    COALESCE((SELECT SUM(br.votos_body) 
                              FROM bodyresultado br 
                              WHERE br.id_mesa_body = m.id_mesa 
                              AND br.candidato_body = $idCandidato 
                              AND br.estado_body != 0), 0) as votos_e14

                FROM mesas m
                INNER JOIN puestos pu ON m.id_puesto_mesa = pu.id_puesto
                INNER JOIN zones z ON pu.idzona_puesto = z.id_zone
                $where
                ORDER BY CAST(m.numero_mesa AS UNSIGNED) ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
