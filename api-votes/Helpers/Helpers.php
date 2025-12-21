<?php
require_once "Libraries/jwt/vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//Retorla la url del proyecto
function base_url()
{
    return BASE_URL;
}

function media()
{
    return BASE_URL . "Assets";
}

//Muestra información formateada
function dep($data)
{
    $format = print_r('<pre>');
    $format .= print_r($data);
    $format .= print_r('</pre>');
    return $format;
}

//Elimina exceso de espacios entre palabras
function strClean($strCadena)
{
    $string = preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $strCadena);
    $string = trim($string); //Elimina espacios en blanco al inicio y al final
    $string = stripslashes($string); // Elimina las \ invertidas
    $string = str_ireplace("<script>", "", $string);
    $string = str_ireplace("</script>", "", $string);
    $string = str_ireplace("<script src>", "", $string);
    $string = str_ireplace("<script type=>", "", $string);
    $string = str_ireplace("SELECT * FROM", "", $string);
    $string = str_ireplace("DELETE FROM", "", $string);
    $string = str_ireplace("INSERT INTO", "", $string);
    $string = str_ireplace("SELECT COUNT(*) FROM", "", $string);
    $string = str_ireplace("DROP TABLE", "", $string);
    $string = str_ireplace("OR '1'='1", "", $string);
    $string = str_ireplace('OR "1"="1"', "", $string);
    $string = str_ireplace('OR ´1´=´1´', "", $string);
    $string = str_ireplace("is NULL; --", "", $string);
    $string = str_ireplace("is NULL; --", "", $string);
    $string = str_ireplace("LIKE '", "", $string);
    $string = str_ireplace('LIKE "', "", $string);
    $string = str_ireplace("LIKE ´", "", $string);
    $string = str_ireplace("OR 'a'='a", "", $string);
    $string = str_ireplace('OR "a"="a', "", $string);
    $string = str_ireplace("OR ´a´=´a", "", $string);
    $string = str_ireplace("OR ´a´=´a", "", $string);
    $string = str_ireplace("--", "", $string);
    $string = str_ireplace("^", "", $string);
    $string = str_ireplace("[", "", $string);
    $string = str_ireplace("]", "", $string);
    $string = str_ireplace("==", "", $string);
    return $string;
}

function jsonResponse($data, int $code)
{
    // Esta línea le dice al navegador que algo salió mal (401, 400, etc.)
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}

function testString(string $data)
{
    $re = '/[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/m';
    if (preg_match($re, $data)) {
        return true;
    } else {
        return false;
    }
}

function testEntero($numero)
{
    $re = '/[0-9]+$/m';
    if (preg_match($re, $numero)) {
        return true;
    } else {
        return false;
    }
}

function testEmail(string $email)
{
    $re = '/[a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/m';
    if (preg_match($re, $email)) {
        return true;
    } else {
        return false;
    }
}

function fntAuthorization(array $arrHeaders)
{
    // Convertir todas las llaves de cabeceras a minúsculas para evitar errores
    $arrHeaders = array_change_key_case($arrHeaders, CASE_LOWER);

    if (empty($arrHeaders['authorization'])) {
        $response = array('status' => false, 'msg' => 'Autorización requerida');
        jsonResponse($response, 401); // Enviamos código 401
        die();
    }

    $tokenBearer = $arrHeaders['authorization'];
    $arrTokenBearer = explode(" ", $tokenBearer);

    // Validar formato "Bearer <token>"
    if (count($arrTokenBearer) < 2 || $arrTokenBearer[0] != 'Bearer') {
        $arrResponse = array('status' => false, 'msg' => 'Formato de token inválido');
        jsonResponse($arrResponse, 401);
        die();
    }

    $token = $arrTokenBearer[1];

    try {
        // Decodificar el token
        $arrPayload = JWT::decode($token, new Key(KEY_SECRET, 'HS512'));

        // OPCIONAL: Podrías retornar el $arrPayload por si el controlador 
        // necesita saber qué usuario está logueado.
        return $arrPayload;

    } catch (\Firebase\JWT\ExpiredException $e) {
        $arrResponse = array('status' => false, 'msg' => 'El token ha expirado');
        jsonResponse($arrResponse, 401);
        die();
    } catch (\Exception $e) {
        $arrResponse = array('status' => false, 'msg' => 'Token inválido o corrupto');
        jsonResponse($arrResponse, 401);
        die();
    }
}

function getTokenApi()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_OAUTH_JWT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_USERPWD, CLIENT_ID . ":" . KEY_SECRET);

    $result = curl_exec($ch);
    $err = curl_error($ch);

    if ($err) {
        $request = "CURL Error #:" . $err;
    } else {
        $request = json_decode($result, true);
    }
    return $request;
}
function token()
{
    $r1 = bin2hex(random_bytes(10));
    $r2 = bin2hex(random_bytes(10));
    $r3 = bin2hex(random_bytes(10));
    $r4 = bin2hex(random_bytes(10));
    $token = $r1 . '-' . $r2 . '-' . $r3 . '-' . $r4;
    return $token;
}

function getPermisos(int $idrol)
{
    // __DIR__ es C:\...\api-votes\Helpers
    // Subimos un nivel para llegar a api-votes y entramos a Models
    $rutaModel = dirname(__DIR__) . "/Models/PermisosModel.php";

    if (file_exists($rutaModel)) {
        require_once($rutaModel);
        $objPermisos = new PermisosModel();
        return $objPermisos->permisosModulo($idrol);
    } else {
        return []; // Retorna un array vacío si no encuentra el archivo
    }
}