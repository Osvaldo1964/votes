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

//Funciones para vincular los encabezados - pies y navegacion del Sitio
function headerAdmin($data = "")
{
    $view_header = 'Views/Template/header_admin.php';
    require_once($view_header);
}
function footerAdmin($data = "")
{
    $view_footer = 'Views/Template/footer_admin.php';
    require_once($view_footer);
}

//Funciones para vincular los encabezados - pies de la tienda
function headerTienda($data = "")
{
    $view_header = 'Views/Template/header_tienda.php';
    require_once($view_header);
}
function footerTienda($data = "")
{
    $view_footer = 'Views/Template/footer_tienda.php';
    require_once($view_footer);
}

//Muestra información formateada
function dep($data)
{
    $format = print_r('<pre>');
    $format .= print_r($data);
    $format .= print_r('</pre>');
    return $format;
}

//Función para activar modales
function getModal(string $nameModal, $data)
{
    $view_modal = "Views/Template/Modals/{$nameModal}.php";
    require_once $view_modal;
}

function getFile(string $url, $data)
{
    ob_start();
    require_once("Views/{$url}.php");
    $file = ob_get_clean();
    return $file;
}

//Envio de correos
function sendEmail($data, $template)
{
    if (ENVIRONMENT == 1) {
        $asunto = $data['asunto'];
        $emailDestino = $data['email'];
        $empresa = NOMBRE_REMITENTE;
        $remitente = EMAIL_REMITENTE;
        $emailCopia = !empty($data['emailCopia']) ? $data['emailCopia'] : "";
        //ENVIO DE CORREO
        $de = "MIME-Version: 1.0\r\n";
        $de .= "Content-type: text/html; charset=UTF-8\r\n";
        $de .= "From: {$empresa} <{$remitente}>\r\n";
        $de .= "Bcc: $emailCopia\r\n";
        ob_start();
        require_once("Views/Template/Email/" . $template . ".php");
        $mensaje = ob_get_clean();
        $send = mail($emailDestino, $asunto, $mensaje, $de);
        return $send;
    } else {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        ob_start();
        require_once("Views/Template/Email/" . $template . ".php");
        $mensaje = ob_get_clean();
        try {
            //Server settings
            $mail->SMTPDebug = 0;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'runalbi.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'suscripciones@runalbi.com';          //SMTP username
            $mail->Password = 'Abtu97?14';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('suscripciones@runalbi.com', 'VITAL FOOD');
            $mail->addAddress($data['email']);     //Add a recipient
            if (!empty($data['emailCopia'])) {
                $mail->addBCC($data['emailCopia']);
            }
            $mail->CharSet = 'UTF-8';
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $data['asunto'];
            $mail->Body = $mensaje;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

function sendMailLocal($data, $template)
{
    //Create an instance; passing `true` enables exceptions

    ob_start();
    require_once("Views/Template/Email/" . $template . ".php");
    $mensaje = ob_get_clean();

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail = new \PHPMailer();

        //CONFIGURAR SMTP y MAIL
        //$mail->SMTPDebug = true;
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Host = "vitalfood.com.co"; // SMTP a utilizar. Por ej. smtp.elserver.com --- pruebas.consamag.com.co

        $mail->IsHTML(true);
        $mail->Username = "ventas@vitalfood.com.co";
        $mail->Password = "ave#9Z243"; // Contraseña
        $mail->Port = 25; // Puerto

        $mail->From = "ventas@vitalfood.com.co"; // Desde donde enviamos (Para mostrar)
        $mail->FromName = "VITALFOOD Colombia";

        $mail->addAddress($data['email']);
        $mail->Subject = $data['asunto'];
        $mail->Body = $mensaje;
        $mail->send();
        $mail->clearAllRecipients();
    } catch (Exception $e) {
        echo "No se pudo enviar el mensaje. Error: {$mail->ErrorInfo}";
    }
}

//Cargar los permisos
function getPermisos(int $idmodulo)
{
    require_once("Models/PermisosModel.php");
    $objPermisos = new PermisosModel();
    if (!empty($_SESSION['userData'])) {
        $idRol = $_SESSION['userData']['idRol'];
        $arrPermisos = $objPermisos->permisosModulo($idRol);
        $permisos = '';
        $permisosMod = '';
        if (count($arrPermisos) > 0) {
            $permisos = $arrPermisos;
            $permisosMod = isset($arrPermisos[$idmodulo]) ? $arrPermisos[$idmodulo] : "";
        }
        $_SESSION['permisos'] = $permisos;
        $_SESSION['permisosMod'] = $permisosMod;
    }
}

function sessionUser()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['login'])) {
        header('Location: ' . base_url() . '/login');
        die();
    }

    // Opcional: Validar tiempo de vida de la sesión local
    if (isset($_SESSION['timeout']) && time() > $_SESSION['timeout']) {
        session_unset();
        session_destroy();
        header('Location: ' . base_url() . '/login?expired=true');
        die();
    }
}

function sessionStart()
{
    session_start();
    $inactive = 1500;
    if (isset($_SESSION['timeout'])) {
        $session_in = time() - $_SESSION['inicio'];
        if ($session_in > $inactive) {
            header("Location:" . BASE_URL . "/logout");
        }
    } else {
        header("Location:" . BASE_URL . "/logout");
    }
}

function uploadImage(array $data, string $name)
{
    $url_temp = $data['tmp_name'];
    $destino = 'Assets/images/uploads/' . $name;
    $move = move_uploaded_file($url_temp, $destino);
    return $move;
}

//Eliminar un archivo
function deleteFile(string $nombre)
{
    unlink('Assets/images/uploads/' . $nombre);
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

function jsonResponse(array $arrData, int $code)
{
    if (is_array($arrData)) {
        //header("HTTP/1.1 ".$code);
        header("Content-Type: application/json");
        echo json_encode($arrData, true);
    }
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

function clear_cadena(string $cadena)
{
    //Reemplazamos la A y a
    $cadena = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $cadena
    );

    //Reemplazamos la E y e
    $cadena = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $cadena
    );

    //Reemplazamos la I y i
    $cadena = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $cadena
    );

    //Reemplazamos la O y o
    $cadena = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $cadena
    );

    //Reemplazamos la U y u
    $cadena = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $cadena
    );

    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç', ',', '.', ';', ':'),
        array('N', 'n', 'C', 'c', '', '', '', ''),
        $cadena
    );
    return $cadena;
}

//Genera una contraseña de 10 caracteres
function passGenerator($length = 10)
{
    $pass = "";
    $longitudPass = $length;
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $longitudCadena = strlen($cadena);

    for ($i = 1; $i <= $longitudPass; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass;
}

//Genera un token
function token()
{
    $r1 = bin2hex(random_bytes(10));
    $r2 = bin2hex(random_bytes(10));
    $r3 = bin2hex(random_bytes(10));
    $r4 = bin2hex(random_bytes(10));
    $token = $r1 . '-' . $r2 . '-' . $r3 . '-' . $r4;
    return $token;
}

//Formato para valores monetarios
function formatMoney($cantidad)
{
    $cantidad = number_format($cantidad, 2, SPD, SPM);
    return $cantidad;
}

function getTokenPaypal()
{
    $payLogin = curl_init(URLPAYPAL . "/v1/oauth2/token");
    curl_setopt($payLogin, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($payLogin, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($payLogin, CURLOPT_USERPWD, IDCLIENTE . ":" . SECRETPAYPAL);
    curl_setopt($payLogin, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    $result = curl_exec($payLogin);
    $err = curl_error($payLogin);
    curl_close($payLogin);
    if ($err) {
        $request = "CURL Error #:" . $err;
    } else {
        $objData = json_decode($result);
        $request = $objData->access_token;
    }
    return $request;
}

function CurlConnectionGet(string $ruta, string $contentType = null, string $token)
{
    $content_type = $contentType != null ? $contentType : "application/x-www-form-urlencoded";
    if ($token != null) {
        $arrHeader = array(
            'Content-Type:' . $content_type,
            'Authorization: Bearer ' . $token
        );
    } else {
        $arrHeader = array('Content-Type:' . $content_type);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
    $result = curl_exec($ch);
    $err = curl_error($ch);

    if ($err) {
        $request = "CURL Error #:" . $err;
    } else {
        $request = json_decode($result);
    }
    return $request;
}

function CurlConnectionPost(string $ruta, string $contentType = null, string $token)
{
    $content_type = $contentType != null ? $contentType : "application/x-www-form-urlencoded";
    if ($token != null) {
        $arrHeader = array(
            'Content-Type:' . $content_type,
            'Authorization: Bearer ' . $token
        );
    } else {
        $arrHeader = array('Content-Type:' . $content_type);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
    $result = curl_exec($ch);
    $err = curl_error($ch);

    if ($err) {
        $request = "CURL Error #:" . $err;
    } else {
        $request = json_decode($result);
    }
    return $request;
}

function Meses()
{
    $meses = array(
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    );
    return $meses;
}

function getCatFooter()
{
    require_once("Models/CategoriasModel.php");
    $objCategoria = new CategoriasModel();
    $request = $objCategoria->getCategoriasFooter();
    return $request;
}

function getInfoPage(int $idpagina)
{
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $sql = "SELECT * FROM post WHERE idpost = $idpagina";
    $request = $con->select($sql);
    return $request;
}

function getPageRout(string $ruta)
{
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $sql = "SELECT * FROM post WHERE ruta = '$ruta' AND status != 0 ";
    $request = $con->select($sql);
    if (!empty($request)) {
        $request['portada'] = $request['portada'] != "" ? media() . "/images/uploads/" . $request['portada'] : "";
    }
    return $request;
}

function viewPage(int $idpagina)
{
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $sql = "SELECT * FROM post WHERE idpost = $idpagina ";
    $request = $con->select($sql);
    if (($request['status'] == 2 and isset($_SESSION['permisosMod']) and $_SESSION['permisosMod']['updPermiso'] == true) or $request['status'] == 1) {
        return true;
    } else {
        return false;
    }
}


function fntAuthorization(array $arrHeaders)
{
    if (empty($arrHeaders['Authorization'])) {
        $response = array('status' => false, 'msg' => 'Autorización requerida');
        jsonResponse($response, 400);
        die();
    } else {
        $tokenBearer = $arrHeaders['Authorization'];
        $arrTokenBearer = explode(" ", $tokenBearer);

        if ($arrTokenBearer[0] != 'Bearer') {
            $arrResponse = array('status' => false, 'msg' => 'Error de autorización');
            jsonResponse($arrResponse, 400);
            die();
        } else {
            $token = $arrTokenBearer[1];

            try {
                $arrPayload = JWT::decode($token, new Key(KEY_SECRET, 'HS512'));
            } catch (\Firebase\JWT\ExpiredException $e) {
                $arrResponse = array('status' => false, 'msg' => $e->getMessage());
                jsonResponse($arrResponse, 400);
                die();
            }
        }
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
