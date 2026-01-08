<?php
class RegistroPublico extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // CORS Headers para permitir peticiones desde cualquier origen (o restringir a chadanalacamara.com)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: POST, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
    }

    public function registrar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                // Recibir datos JSON
                $data = json_decode(file_get_contents("php://input"), true);

                if (empty($data)) {
                    jsonResponse(['status' => false, 'msg' => 'Datos vacíos.'], 400);
                    die();
                }

                // 1. Verificar reCAPTCHA
                $recaptchaToken = isset($data['recaptcha_token']) ? $data['recaptcha_token'] : '';

                if (empty($recaptchaToken)) {
                    jsonResponse(['status' => false, 'msg' => 'Verificación robótica fallida (Token vacío).'], 403);
                    die();
                }

                if (!$this->verifyRecaptcha($recaptchaToken)) {
                    jsonResponse(['status' => false, 'msg' => 'Detectamos tráfico sospechoso. Intenta de nuevo.'], 403);
                    die();
                }

                // 2. Validar Campos Obligatorios
                if (empty($data['ident_elector']) || empty($data['nom1_elector']) || empty($data['ape1_elector']) || empty($data['telefono_elector'])) {
                    jsonResponse(['status' => false, 'msg' => 'Faltan datos obligatorios.'], 200);
                    die();
                }

                // 3. Preparar Datos
                $strCedula = strClean($data['ident_elector']);
                $strApe1 = strtoupper(strClean($data['ape1_elector']));
                $strApe2 = isset($data['ape2_elector']) ? strtoupper(strClean($data['ape2_elector'])) : '';
                $strNom1 = strtoupper(strClean($data['nom1_elector']));
                $strNom2 = isset($data['nom2_elector']) ? strtoupper(strClean($data['nom2_elector'])) : '';
                $strTelefono = strClean($data['telefono_elector']);
                $strEmail = isset($data['email_elector']) ? strClean($data['email_elector']) : '';
                $intDpto = isset($data['dpto_elector']) ? intval($data['dpto_elector']) : 0;
                $intMuni = isset($data['muni_elector']) ? intval($data['muni_elector']) : 0;
                $strDireccion = isset($data['direccion_elector']) ? strtolower(strClean($data['direccion_elector'])) : '';

                // ASIGNACIÓN AUTOMÁTICA DE LÍDER WEB
                // Por defecto ID 0, o puedes crear un usuario "WEB" en la BD y poner su ID aquí.
                $intLider = 0;

                $intEstado = 1; // Activo por defecto
                $intInsc = 0;

                // 4. Insertar usando el Modelo existente de Electores
                // IMPORTANTE: Necesitamos instanciar el modelo de Electores.
                // Como este es un controlador nuevo, debemos cargar el modelo manualmente o heredar.
                // Framework actual carga modelo automático si se llama igual (RegistroPublicoModel).
                // Opción rápida: Usar ElectoresModel.

                require_once("Models/ElectoresModel.php");
                $electoresModel = new ElectoresModel();

                $request_elector = $electoresModel->insertElector(
                    $strCedula,
                    $strApe1,
                    $strApe2,
                    $strNom1,
                    $strNom2,
                    $strTelefono,
                    $strEmail,
                    $intDpto,
                    $intMuni,
                    $strDireccion,
                    $intLider,
                    $intEstado,
                    $intInsc
                );

                if ($request_elector > 0) {
                    jsonResponse(['status' => true, 'msg' => '¡Registro exitoso! Gracias por tu apoyo.'], 200);
                } else if ($request_elector == 'exist') {
                    jsonResponse(['status' => false, 'msg' => '¡Ya estás registrado! Gracias por confirmar tu apoyo.'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'Error al guardar. Intenta más tarde.'], 500);
                }

            } else {
                jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    private function verifyRecaptcha($token)
    {
        $secretKey = RECAPTCHA_SECRET_KEY;

        // Si no se han configurado las claves, saltar validación (SOLO DESARROLLO)
        if ($secretKey == "CLAVE_SECRETA_AQUI")
            return true;

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $token
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result);

        // Google devuelve score entre 0.0 y 1.0. 0.5 es un buen umbral.
        return $json->success && $json->score >= 0.5;
    }
}
?>