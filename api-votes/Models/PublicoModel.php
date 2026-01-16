<?php

class PublicoModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectConsultaPublica(string $cedula)
    {
        // NOTA IMPORTANTE DE RENDIMIENTO:
        // Esta consulta debe ser optimizada. Estamos asumiendo la estructura actual.
        // Si ya se hizo la normalización (Separación de Mesas), el JOIN debe ser con la tabla maestra de mesas.
        // Si NO se ha hecho, se lee directo de 'places'.
        // Asumiendo estructura actual basada en PROJECT_STATUS (aun tabla places desnormalizada):

        // Consulta corregida para estructura normalizada (places -> mesas -> puestos)
        $sql = "SELECT p.ident_place as identificacion, 
                       CONCAT(IFNULL(p.nom1_place,''), ' ', IFNULL(p.nom2_place,''), ' ', IFNULL(p.ape1_place,''), ' ', IFNULL(p.ape2_place,'')) as nombres,
                       'Magdalena' as departamento, 
                       'Santa Marta' as municipio,
                       pu.nombre_puesto as puesto, 
                       m.numero_mesa as mesa,
                       pu.nombre_puesto as direccion_puesto
                FROM places p
                INNER JOIN mesas m ON p.id_mesa_new = m.id_mesa
                INNER JOIN puestos pu ON m.id_puesto_mesa = pu.id_puesto
                WHERE p.ident_place = '$cedula'";

        $request = $this->select($sql, []);
        return $request;
    }

    public function updateVotoPublico(string $cedula)
    {
        // 1. Verificar si existe en 'electores' (Base de datos propia)
        $sqlCheck = "SELECT id_elector, poll_elector FROM electores WHERE ident_elector = '$cedula'";
        $requestCheck = $this->select($sqlCheck, []);

        if (!empty($requestCheck)) {
            // Caso A: Ya existe en electores
            if ($requestCheck['poll_elector'] == 1) {
                return "already_voted";
            }
            // Marcar voto
            $sql = "UPDATE electores SET poll_elector = 1 WHERE ident_elector = '$cedula'";
            $request = $this->update($sql, array());
            return $request ? "ok" : "error";
        }

        // Caso B: No existe en electores, buscar en Censo (places)
        // Usamos la misma lógica de JOIN que en consultar para obtener datos frescos
        $sqlCenso = "SELECT p.ident_place, p.nom1_place, p.nom2_place, p.ape1_place, p.ape2_place
                     FROM places p
                     WHERE p.ident_place = '$cedula'";
        $requestCenso = $this->select($sqlCenso, []);

        if (empty($requestCenso)) {
            return "not_found"; // No está ni en electores ni en censo
        }

        // Caso C: Existe en Censo -> "Creación en Caliente"
        // Insertamos en electores con datos por defecto

        $identificacion = $requestCenso['ident_place'];
        $nombres = $requestCenso['nom1_place']; // Asumimos nom1 como nom1
        $nom2 = $requestCenso['nom2_place'];
        $ape1 = $requestCenso['ape1_place'];
        $ape2 = $requestCenso['ape2_place'];

        // Defaults
        $sexo = '';
        $telefono = '0000000000'; // Default
        $email = '';
        $direccion = 'Registrado via Web';
        $lider = 1; // Default Leader ID (Juan Alberto)
        $dpto = 15; // Default Magdalena
        $muni = 570; // Default Santa Marta (o lo que corresponda)
        $zona = 0;
        $barrio = 0;
        $poll = 1; // Ya votó
        $estado = 1;

        $insert = "INSERT INTO electores (ident_elector, ape1_elector, ape2_elector, nom1_elector, nom2_elector, sexo_elector, telefono_elector, email_elector, direccion_elector, lider_elector, dpto_elector, muni_elector, zona_elector, barrio_elector, poll_elector, estado_elector) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $arrData = array($identificacion, $ape1, $ape2, $nombres, $nom2, $sexo, $telefono, $email, $direccion, $lider, $dpto, $muni, $zona, $barrio, $poll, $estado);
        $requestInsert = $this->insert($insert, $arrData);

        return $requestInsert ? "ok" : "error";
    }
}
