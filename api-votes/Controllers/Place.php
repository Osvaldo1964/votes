<?php
    class Place extends Controllers{

        public function __construct()
        {
            parent::__construct();
        }

        public function place($idelector)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($idelector)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        jsonResponse($response,400);
                        die();
                    }
                    $arrUser = $this->model->getPlace($idelector);
                    if(empty($arrUser))
                    {
                        $response = array('status' => false , 'msg' => 'Registro no encontrado');
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrUser);
                    }
                    $code = 200;                    
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;
                }

                jsonResponse($response,$code);
                die();

            } catch (Exception $e) {
                $arrResponse = array('status' => false , 'msg' => $e->getMessage());
                jsonResponse($arrResponse,400);
            }

            die();
            
        }

        public function registro(){
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    //================= Validar token ===================
                    $arrHeaders = getallheaders();
                    $reesponse = fntAuthorization($arrHeaders);
                    //====================================================
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
                    
                    $request = $this->model->setUser($strNombres,
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
                $arrResponse = array('status' => false , 'msg' => $e->getMessage());
                jsonResponse($arrResponse,400);
            }
             die();

        }

        public function actualizar($idusuario)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "PUT")
                {
                    //================= Validar token ===================
                    $arrHeaders = getallheaders();
                    $reesponse = fntAuthorization($arrHeaders);
                    //====================================================
                    $data = json_decode(file_get_contents('php://input'),true);
                    if(empty($idusuario) or !is_numeric($idusuario)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }
                    
                    if(empty($data['nombres']) or !testString($data['nombres']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los nombres');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($data['apellidos']) or !testString($data['apellidos']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los apellidos');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($data['email']) or !testEmail($data['email']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el email');
                        jsonResponse($response,200);
                        die();
                    }
                   
                    $strNombres = ucwords(strClean($data['nombres']));
                    $strApellidos = ucwords(strClean($data['apellidos']));
                    $strEmail = strClean($data['email']);
                    $strPassword = !empty($data['password']) ? hash("SHA256",$data['password']) : "";

                    $buscar_usuario = $this->model->getUsuario($idusuario);
                    if(empty($buscar_usuario))
                    {
                        $response = array('status' => false , 'msg' => 'El usuario no existe');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }
                    
                    $request = $this->model->putUser($idusuario,
                                                    $strNombres,
                                                    $strApellidos,
                                                    $strEmail, 
                                                    $strPassword);
                    if($request > 0)
                    {
                        $arrUser = array('idusuario' => $idusuario,
                                        'nombres' => $strNombres,
                                        'apellidos' => $strApellidos,
                                        'email' => $strEmail
                                        );
                        $response = array('status' => true , 'msg' => 'Datos actualizados correctamente', 'data' => $arrUser);                
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
                $arrResponse = array('status' => false , 'msg' => $e->getMessage());
                jsonResponse($arrResponse,400);
            }
            die();

        }

        public function places()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    $arrData = $this->model->getPlaces();
                    if(empty($arrData))
                    {
                        $response = array('status' => false , 'msg' => 'No hay datos para mostrar', 'data' => '');
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados ', 'data' =>  $arrData);
                    }
                    $code = 200;
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;
                }
                jsonResponse($response,$code);
                die();

            } catch (Exception $e) {
                $arrResponse = array('status' => false , 'msg' => $e->getMessage());
                jsonResponse($arrResponse,400);
            }
            die();
        }
    }
?>