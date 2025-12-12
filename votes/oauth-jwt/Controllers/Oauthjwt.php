<?php
    require_once "Libraries/jwt/vendor/autoload.php";
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class Oauthjwt extends Controllers{

        public function __construct()
        {
            parent::__construct();
        }

        public function registrocliente(){
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    $_POST = json_decode(file_get_contents('php://input'),true);
                    
                    if(empty($_POST['nombres']) or !testString($_POST['nombres']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los nombres');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['apellidos']) or !testString($_POST['apellidos']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los apellidos');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['email']) or !testEmail($_POST['email']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el email');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['password'])){
                        $response = array('status' => false , 'msg' => 'El password es requerido');
                        jsonResponse($response,200);
                        die();
                    }

                    $strNombres = ucwords(strClean($_POST['nombres']));
                    $strApellidos = ucwords(strClean($_POST['apellidos']));
                    $strEmail = strClean($_POST['email']);
                    $strPassword = hash("SHA256",$_POST['password']);
                    
                    $request = $this->model->setCliente($strNombres,
                                                    $strApellidos,
                                                    $strEmail, 
                                                    $strPassword);
                    if($request > 0)
                    {
                        $arrUser = array('id' => $request);
                        $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrUser);
                    }else{
                        $response = array('status' => false , 'msg' => 'El email ya existe');
                    }                             
                    $code = 200;
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;
                }
                
                jsonResponse($response,$code);
                die();

            } catch (Exception $e) {
                    echo "Error en el proceso: ". $e->getMessage();
            }
             die();
        }

        public function crearapp(){
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    $_POST = json_decode(file_get_contents('php://input'),true);
                    if(empty($_POST['email']) or !testEmail($_POST['email']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el email');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['scope'])){
                        $response = array('status' => false , 'msg' => 'Nombre de APP requerido');
                        jsonResponse($response,200);
                        die();
                    }

                    $strEmail = strClean($_POST['email']);
                    $strScope = strClean($_POST['scope']);
                    $arrCliente = $this->model->getCliente($strEmail);
                    if(empty($arrCliente)){
                        $response = array('status' => false , 'msg' => 'El usuario no existe');
                    }else{
                        $intClientejwtId = $arrCliente['id_clientejwt'];
                        $nombreCompleto = $arrCliente['nombres'].' '.$arrCliente['apellidos'];
                        $strScopeApp =  $strEmail.' '.$strScope;

                        $client_id = hash("SHA256",$nombreCompleto).'-'.hash("SHA256",$strScopeApp);
                        $key_secret = hash("SHA256",$strScopeApp).'-'.hash("SHA256",$nombreCompleto);

                        $request = $this->model->setScope($strScope,
                                                        $client_id,
                                                        $key_secret,
                                                        $intClientejwtId
                                                    );
                        if($request > 0){
                            $arrScope = array('id' => $request,
                                        'email' => $strEmail,
                                        'client_id' => $client_id,
                                        'key_secret' => $key_secret,
                                        'scope' => $strScope
                                        );
                            $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrScope);
                        }else{
                            $response = array('status' => false , 'msg' => 'La aplicación ya existe');
                        }
                    }
                    $code = 200;                    
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;
                }
                
                jsonResponse($response,$code);
                die();
            } catch (Exception $e) {
                echo "Error en el proceso: ". $e->getMessage();
            }
            die();
        }

        public function token(){
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    if(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) ){
                        $response = array('status' => false , 'msg' => 'Autorización requerida');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['grant_type']) || $_POST['grant_type'] != 'client_credentials'){
                        $response = array('status' => false , 'msg' => 'Error en los parámetros');
                        jsonResponse($response,200);
                        die();
                    }

                    $client_id = $_SERVER['PHP_AUTH_USER'];
                    $key_secret = $_SERVER['PHP_AUTH_PW'];

                    $request = $this->model->getScopeAuth($client_id,$key_secret); 

                    if($request > 0){
                        $idScope = $request['id_scope'];
                        $scope = $request['scope'];
                        $idclientejwt = $request['id_clientejwt'];
                        $email = $request['email'];
                        $time = time();
                        // 1 M = 60
                        // 1 H = 3600
                        // 1 D = 86,400
                        //$expires_in = $time + (60 * 60 * 24); //1 días ó 24 horas
                        $expires_in = $time + (3600); // 1 hora
                        $arrPayLoad = array('id_sp' => $idScope,
                                        'scope' => $scope,
                                        'email' => $email,
                                        'iat' => $time,
                                        'exp' => $expires_in
                                        ); 
                        $tokenJWT = JWT::encode($arrPayLoad, $key_secret, 'HS512');   
                        $insertToken = $this->model->setTokenDB($idclientejwt,$idScope,$tokenJWT,$expires_in);
                        if($insertToken > 0){
                            $arrData = array('id' => $idScope,
                                        'access_token' => $tokenJWT,
                                        'token_type' => "Bearer",
                                        'expires_in' => $expires_in,
                                        'scope' => $scope
                                        );
                            $response = array('status' => true , 'msg' => 'Token generado', 'data' => $arrData);
                        }else{
                            $response  = array('status' => false , 'msg' => 'Error al registrar token, consulte al administrador');
                        }
                    }else{
                        $response = array('status' => false , 'msg' => 'Error de autenticación');
                    }

                    $code = 200;
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;
                }
                jsonResponse($response,$code);
                die();
            } catch (Exception $e) {
                echo "Error en el proceso: ". $e->getMessage();
            }
            die();
        }

        public function tokenValidate($token){
           try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($token))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los parámetros');
                        jsonResponse($response,200);
                        die(); 
                    }
                    $tokenJwt = strClean($token);
                    $request = $this->model->getToken($tokenJwt);
                    
                    if(empty($request)){
                        $response = array('status' => false , 'msg' => 'El token no es válido');
                    }else{
                        $keySecret = $request['key_secret'];
                        $token = $request['access_token'];
                        $expires_in = $request['expirres_in'];
                        $decoded = JWT::decode($token, new Key($keySecret, 'HS512'));
                        $arrData = array(
                                        'access_token' => $token,
                                        'token_type' => "Bearer",
                                        'expires_in' => $expires_in
                                        );
                        $response = array('status' => true , 'msg' => 'Token válido','data'=>$arrData);
                    }
                    
                    $code = 200;
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;
                }
                jsonResponse($response,$code);
                die();
           } catch (Exception $e) {
                $response = array('status' => false , 'msg' => $e->getMessage());
                jsonResponse($response,200);
           }
           die();
        }



    }


?>