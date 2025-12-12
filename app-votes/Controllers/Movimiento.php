<?php
    class Movimiento extends Controllers{

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

        public function registroTipoMovimiento()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    $_POST = json_decode(file_get_contents('php://input'),true);
                    if(empty($_POST['movimiento']))
                    {
                        $response = array('status' => false , 'msg' => 'El movimiento es requerido');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['tipo_movimiento']) or ($_POST['tipo_movimiento'] != 1 and $_POST['tipo_movimiento'] != 2))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el Tipo Movimiento');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['descripcion']))
                    {
                        $response = array('status' => false , 'msg' => 'La descripcion es requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    $strMovimiento = ucwords(strClean($_POST['movimiento']));
                    $intTipoMovimiento = $_POST['tipo_movimiento'];
                    $strDescripcion = strClean($_POST['descripcion']);
                    
                    $request = $this->model->setTipoMovimiento($strMovimiento,$intTipoMovimiento,$strDescripcion);
                    if($request > 0)
                    {
                        $arrMovimiento = array("idtipomovimiento" => $request,
                                                "movimiento" =>  $strMovimiento,
                                                "tipo_movimiento" => $intTipoMovimiento,
                                                "descripcion" => $strDescripcion );
                        $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrMovimiento);
                    }else{
                        $response = array('status' => false , 'msg' => 'El tipo movimiento ya existe');
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

        public function tiposMovimiento()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    $arrData = $this->model->getTiposMovimiento();
                    if(empty($arrData))
                    {
                        $response = array('status' => false , 'msg' => 'No hay datos para mostrar', 'data' => "");
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrData);
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

        // ============ Métodos Movimientos

        public function registroMovimiento()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    $_POST = json_decode(file_get_contents('php://input'),true);

                    if(empty($_POST['cuentaid']) or !is_numeric($_POST['cuentaid']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el id cuenta');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['tipomovimientoid']) or !is_numeric($_POST['tipomovimientoid']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el id Tipo Movimiento');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['movimiento']) or ($_POST['movimiento'] != 1 and $_POST['movimiento'] != 2))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el Movimiento');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['monto']) or !is_numeric($_POST['monto']))
                    {
                        $response = array('status' => false , 'msg' => 'Error en el monto');
                        jsonResponse($response,200);
                        die();
                    }
                    if(empty($_POST['descripcion']) )
                    {
                        $response = array('status' => false , 'msg' => 'Error en la descripción');
                        jsonResponse($response,200);
                        die();
                    }

                    $intCuentaID = strClean($_POST['cuentaid']);
                    $intMovimientoID = strClean($_POST['tipomovimientoid']);
                    $intMovimiento = strClean($_POST['movimiento']);
                    $strMonto = strClean($_POST['monto']);
                    $strDescripcion = strClean($_POST['descripcion']);
                    $arrMovimiento = $this->model->setMovimiento($intCuentaID,$intMovimientoID,$intMovimiento,$strMonto,$strDescripcion);
                    if(is_numeric($arrMovimiento) and $arrMovimiento > 0) 
                    {
                        $arrMovimiento = array('idMovimiento' => $arrMovimiento);
                        $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrMovimiento); 
                    }else{
                        $response = array('status' => false , 'msg' => 'No es posible registrar el movimiento','msg_tecnito' =>$arrMovimiento );
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

        public function movimiento($idmovimiento)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($idmovimiento) or !is_numeric($idmovimiento)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        jsonResponse($response,400);
                        die();
                    }
                    $idmovimiento = strClean($idmovimiento);
                    $arrMovimiento = $this->model->getMovimiento($idmovimiento);
                    if(empty($arrMovimiento))
                    {
                        $response = array('status' => false , 'msg' => 'Registro no encontrado'); 
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrMovimiento);
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

        public function movimientos()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    $arrMovimientos = $this->model->getMovimientos();
                    if(empty($arrMovimientos))
                    {
                        $response = array('status' => false , 'msg' => 'No hay datos para mostrar', 'data' => "");
                    }else{
                        $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $arrMovimientos);
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

        public function anular($idmovimiento)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "DELETE")
                {
                    if(empty($idmovimiento) or !is_numeric($idmovimiento)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }
                    
                    $request = $this->model->getMovimiento($idmovimiento);
                    if(empty($request))
                    {
                        $response = array('status' => false , 'msg' => 'El registro no existe o ya fue eliminado');
                        jsonResponse($response,400);
                        die();
                    }else{
                        $request = $this->model->anularMovimiento($idmovimiento);
                        if(!empty($request))
                        {
                            $response = array('status' => true , 'msg' => 'Movimiento anulado', 'data' =>  $request[0]); 
                        }else{
                            $response = array('status' => false , 'msg' => 'No es posible eliminar el movimiento');
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
        
        


    }


?>