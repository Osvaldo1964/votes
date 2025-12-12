<?php
    class Cuenta extends Controllers{

        public function __construct()
        {
            try {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================
            } catch (\Throwable $e) {
                $arrResponse = array('status' => false , 'msg' => $e->getMessage());
                jsonResponse($arrResponse,400);
                die();
            }
            parent::__construct();
        }

        public function cuenta($idcuenta)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($idcuenta) or !is_numeric($idcuenta)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        jsonResponse($response,400);
                        die();
                    }
                    $arrCuenta = $this->model->getCuenta($idcuenta);
                    if(empty($arrCuenta))
                    {
                        $response = array('status' => false , 'msg' => 'Registro no encontrado');
                    }else{
                        $arrMovimientos = $this->model->getMovimientos($idcuenta);
                        $arrCuenta['movimientos'] = $arrMovimientos;
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrCuenta);   
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

        public function cuentas()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    $arrCuentas = $this->model->getCuentas();
                    if(empty($arrCuentas))
                    {
                        $response = array('status' => false , 'msg' => 'No hay datos para mostrar', 'data' => ""); 
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrCuentas);
                    }
                    $code = 200;
                }else{
                    $response = array('status' => false , 'msg' => 'Error en la solicitud '.$method);
                    $code = 400;  
                }
                jsonResponse($response,$code);
                die();
            } catch (\Throwable $th) {
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

                    if(empty($_POST['clienteId']) or !is_numeric($_POST['clienteId']))
                    {
                        $response = array('status' => false , 'msg' => 'El cliente es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['productoId']) or !is_numeric($_POST['productoId']))
                    {
                        $response = array('status' => false , 'msg' => 'El producto es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['frecuenciaId']) or !is_numeric($_POST['frecuenciaId']))
                    {
                        $response = array('status' => false , 'msg' => 'La frecuencia es requerida');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['monto']) or !is_numeric($_POST['monto']))
                    {
                        $response = array('status' => false , 'msg' => 'El monto es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['cuotas']) or !is_numeric($_POST['cuotas']))
                    {
                        $response = array('status' => false , 'msg' => 'La cuota es requerida');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['monto_cuota']) or !is_numeric($_POST['monto_cuota']))
                    {
                        $response = array('status' => false , 'msg' => 'El monto cuota es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['cargo']) or !is_numeric($_POST['cargo']))
                    {
                        $response = array('status' => false , 'msg' => 'El cargo es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['saldo']) or !is_numeric($_POST['saldo']))
                    {
                        $response = array('status' => false , 'msg' => 'El saldo es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    
                    $intClienteId = strClean($_POST['clienteId']);
                    $intProductoId = strClean($_POST['productoId']);
                    $intFrecuenciaId = strClean($_POST['frecuenciaId']);
                    $strMonto = strClean($_POST['monto']);
                    $strCuotas = strClean($_POST['cuotas']);
                    $strMontoCuotas = strClean($_POST['monto_cuota']);
                    $strCargo = strClean($_POST['cargo']);
                    $strSaldo = strClean($_POST['saldo']);

                    $request = $this->model->setCuenta($intClienteId,
                                                        $intProductoId, 
                                                        $intFrecuenciaId, 
                                                        $strMonto,
                                                        $strCuotas,
                                                        $strMontoCuotas,
                                                        $strCargo,
                                                        $strSaldo);

                    if(is_numeric($request) and $request > 0) 
                    {
                        $arrCuenta = array('idContrado' => $request);
                        $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrCuenta); 
                    }else{
                        $response = array('status' => false , 'msg' => 'No es posible crear el contrato','msg_tecnito' =>$request );
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

        public function orden($idcuenta)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($idcuenta) or !is_numeric($idcuenta)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        jsonResponse($response,400);
                        die();
                    }
                    $arrCuenta = $this->model->getCuenta($idcuenta);
                    if(empty($arrCuenta))
                    {
                        $response = array('status' => false , 'msg' => 'Registro no encontrado');
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrCuenta);   
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