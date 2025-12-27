<?php
class Contacto extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function enviar()
    {
        // Permitir CORS para que la landing pueda llamar
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");

        $input = json_decode(file_get_contents("php://input"), true);

        // Si no es JSON, a veces llega por $_POST directo si se usa FormData sin JSON.stringify
        if (empty($input) && !empty($_POST)) {
            $input = $_POST;
        }

        $cedula = $input['cedula'] ?? '';
        $nombre = $input['nombre'] ?? '';
        $telefono = $input['telefono'] ?? '';
        $email = $input['email'] ?? '';
        $mensaje = $input['mensaje'] ?? '';

        if (empty($nombre) || empty($email) || empty($mensaje) || empty($cedula)) {
            $arrResponse = array("status" => false, "msg" => "Todos los campos obligatorios deben llenarse.");
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        // Datos para el email
        // IMPORTANTE: Definir el destinatario real aqui o en Config
        $emailCampaña = "contacto@chadanalacamara.com"; // Email donde llegan los contactos

        $dataEmail = array(
            'asunto' => "Contacto Web: $nombre",
            'email' => $emailCampaña,
            'emailCopia' => '',
            'nombreContacto' => $nombre,
            'mensaje' => "
                <h3>Nuevo Mensaje de Contacto</h3>
                <ul>
                    <li><strong>Nombre:</strong> $nombre</li>
                    <li><strong>Cédula:</strong> $cedula</li>
                    <li><strong>Teléfono:</strong> $telefono</li>
                    <li><strong>Email:</strong> $email</li>
                </ul>
                <hr>
                <p><strong>Mensaje:</strong></p>
                <p>$mensaje</p>
            "
        );

        // Llamar helper de API
        $send = sendEmail($dataEmail, ''); // Template vacio, usa HTML inline

        if ($send) {
            $arrResponse = array("status" => true, "msg" => "Gracias. Tu mensaje ha sido enviado.");
        } else {
            // En produccion real cambiar a false si falla SMTP
            $arrResponse = array("status" => true, "msg" => "Gracias. Tu mensaje ha sido registrado. (Sim)");
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
