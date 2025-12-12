<?php
    class Cliente extends Controllers{

        public function __construct()
        {
            parent::__construct();
        }

        public function cliente($idcliente)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($idcliente) or !is_numeric($idcliente)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }

                    $arrCliente = $this->model->getCliente($idcliente);
                    if(empty($arrCliente))
                    {
                        $response = array('status' => false , 'msg' => 'Registro no encontrado');
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrCliente);
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

        public function registro()
        {
           try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];

                if($method == "POST")
                {
                    $_POST = json_decode(file_get_contents('php://input'),true);

                    if(empty($_POST['identificacion']))
                    {
                        $response = array('status' => false , 'msg' => 'La identificación es requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['nombres']) or !testString($arrData['nombres']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los nombres');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['apellidos']) or !testString($arrData['apellidos']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los apellidos');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['telefono']) or !testEntero($arrData['telefono']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el teléfono');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['email']) or !testEmail($arrData['email']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el email');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($_POST['direccion']))
                    {
                        $response = array('status' => false , 'msg' => 'La direccion es requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    $strIdentificacion = $_POST['identificacion'];
                    $strNombres = ucwords(strtolower($_POST['nombres']));
                    $strApellidos = ucwords(strtolower($_POST['apellidos']));
                    $intTelefono = $_POST['telefono'];
                    $strEmail = strtolower($_POST['email']);
                    $strDireccion = $_POST['direccion'];
                    $strNit = !empty($_POST['nit']) ? strClean($_POST['nit']) : "";
				    $strNomFiscal = !empty($_POST['nombrefiscal']) ? strClean($_POST['nombrefiscal']) : "";
				    $strDirFiscal = !empty($_POST['direccionfiscal']) ? strClean($_POST['direccionfiscal']) : "";
                   
                    $request = $this->model->setCliente($strIdentificacion,
                                                        $strNombres,
                                                        $strApellidos,
                                                        $intTelefono,
                                                        $strEmail,
                                                        $strDireccion,
                                                        $strNit,
                                                        $strNomFiscal,
                                                        $strDirFiscal);
                    
                    if($request > 0)
                    {
                        $arrCliente = array('idcliente' => $request,
										'identificacion' => $strIdentificacion,
										'nombres' => $strNombres,
										'apellidos' => $strApellidos,
										'telefono' => $intTelefono,
										'email' => $strEmail,
										'direccion' => $strDireccion,
										'nit' => $strNit,
										'nombreFiscal' => $strNomFiscal,
										'direccionFiscal' => $strDirFiscal
										);
                        $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrCliente);                
                    }else{
                        $response = array('status' => false , 'msg' => 'La identificación o el email ya existe');
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

        public function clientes()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    $arrData = $this->model->getClientes();
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
                echo "Error en el proceso: ". $e->getMessage();
            }
            die();
        }

        public function actualizar($idcliente)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];                
                if($method == "PUT")
                {
                    $arrData = json_decode(file_get_contents('php://input'),true);

                    if(empty($idcliente) or !is_numeric($idcliente)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }

                    if(empty($arrData['identificacion']))
                    {
                        $response = array('status' => false , 'msg' => 'La identificación es requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['nombres']) or !testString($arrData['nombres']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los nombres');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['apellidos']) or !testString($arrData['apellidos']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en los apellidos');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['telefono']) or !testEntero($arrData['telefono']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el teléfono');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['email']) or !testEmail($arrData['email']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el email');
                        jsonResponse($response,200);
                        die();
                    }

                    if(empty($arrData['direccion']))
                    {
                        $response = array('status' => false , 'msg' => 'La direccion es requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    $strIdentificacion = $arrData['identificacion'];
                    $strNombres = ucwords(strtolower($arrData['nombres']));
                    $strApellidos = ucwords(strtolower($arrData['apellidos']));
                    $intTelefono = $arrData['telefono'];
                    $strEmail = strtolower($arrData['email']);
                    $strDireccion = $arrData['direccion'];
                    $strNit = !empty($arrData['nit']) ? strClean($arrData['nit']) : "";
				    $strNomFiscal = !empty($arrData['nombrefiscal']) ? strClean($arrData['nombrefiscal']) : "";
				    $strDirFiscal = !empty($arrData['direccionfiscal']) ? strClean($arrData['direccionfiscal']) : "";

                    $buscar_cliente = $this->model->getCliente($idcliente);
                    if(empty($buscar_cliente))
                    {
                        $response = array('status' => false , 'msg' => 'El cliente no existe');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }
                    
                    $request = $this->model->putCliente($idcliente,
                                                        $strIdentificacion,
                                                        $strNombres,
                                                        $strApellidos,
                                                        $intTelefono,
                                                        $strEmail,
                                                        $strDireccion,
                                                        $strNit,
                                                        $strNomFiscal,
                                                        $strDirFiscal);
                    if($request)
                    {
                        $arrCliente = array('idcliente' => $idcliente,
                                            'identificacion' => $strIdentificacion,
                                            'nombres' => $strNombres,
                                            'apellidos' => $strApellidos,
                                            'telefono' => $intTelefono,
                                            'email' => $strEmail,
                                            'direccion' => $strDireccion,
                                            'nit' => $strNit,
                                            'nombreFiscal' => $strNomFiscal,
                                            'direccionFiscal' => $strDirFiscal
                                            );
                        $response = array('status' => true , 'msg' => 'Datos Actualizados correctamente', 'data' => $arrCliente);
                    }else{
                        $response = array('status' => true , 'msg' => 'La identificación o el email ya existe');
                        
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

        public function eliminar($idcliente)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "DELETE")
                {
                    if(empty($idcliente) or !is_numeric($idcliente)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }

                    $buscar_cliente = $this->model->getCliente($idcliente);
                    if(empty($buscar_cliente))
                    {
                        $response = array('status' => false , 'msg' => 'El cliente no existe o ya fue eliminado');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }

                    $request = $this->model->deleteCliente($idcliente);
                    if($request)
                    {
                        $response = array('status' => true , 'msg' => 'Registro eliminado');
                    }else{
                        $response = array('status' => false , 'msg' => 'No es posible eliminar el registro');
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


    }


?>