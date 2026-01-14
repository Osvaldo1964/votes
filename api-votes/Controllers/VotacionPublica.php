<?php
require_once("Models/ElectoresModel.php"); // Reutilizamos el modelo

class VotacionPublica extends Controllers
{
    public function __construct()
    {
        // NO llamamos a parent::__construct() si este valida Auth.
        // Pero Controllers base suele cargar vistas y modelos.
        // Si Controllers valida sesión en constructor, debemos evitarlo.

        // Revisando Controllers.php... asumo que es seguro instanciar.
        // Si Controllers.php tiene "session_start" o validaciones, hay que cuidar.
        // Por lo general, la validación se hace en el método del controlador hijo (como en Electores __construct -> fntAuthorization).

        parent::__construct();
        // NO validamos token aquí. Es público.

        // Instancia manual del modelo si no se carga auto.
        // El framework parece cargar modelo basado en nombre clase 'VotacionPublicaModel'.
        // Pero queremos usar 'ElectoresModel'.
        $this->model = new ElectoresModel();
    }

    public function getValidaElector($id_elector)
    {
        // Copia de Electores::getValidaElector pero pública
        if (empty($id_elector)) {
            jsonResponse(['status' => false, 'msg' => 'Identificación obligatoria'], 200);
            die();
        }

        $strCedula = strClean($id_elector);
        $requestPlace = $this->model->selectPlace($strCedula);
        $checkDuplicate = $this->model->selectElectorByIdent($strCedula);
        $isRegistered = !empty($checkDuplicate);

        if (empty($requestPlace)) {
            $response = array('status' => false, 'msg' => 'Datos no encontrados en el censo');
        } else {
            $response = array(
                'status' => true,
                'msg' => 'Datos encontrados',
                'data' => $requestPlace,
                'is_registered' => $isRegistered,
                'allow_vote' => true,
                'elector_data' => $checkDuplicate
            );
        }
        jsonResponse($response, 200);
        die();
    }

    public function setVoto()
    {
        if ($_POST) {
            if (empty($_POST['identificacion'])) {
                jsonResponse(['status' => false, 'msg' => 'Identificación obligatoria'], 200);
            }
            $strIdentificacion = strClean($_POST['identificacion']);

            // Reutiliza la lógica de electores (que ahora tiene auto-registro)
            $request = $this->model->updatePollElector($strIdentificacion);

            if ($request === "voted") {
                jsonResponse(['status' => false, 'msg' => '¡Atención! Este elector YA registró su voto.'], 200);
            } else if ($request === "not_found") {
                jsonResponse(['status' => false, 'msg' => 'Elector no encontrado o inactivo.'], 200);
            } else if ($request) {
                jsonResponse(['status' => true, 'msg' => 'Voto registrado correctamente.'], 200);
            } else {
                jsonResponse(['status' => false, 'msg' => 'Error al registrar el voto.'], 200);
            }
        }
        die();
    }
}
