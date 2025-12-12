<?php
    class Frecuencia extends Controllers{

        public function __construct()
        {
            parent::__construct();
        }

        public function frecuencia($idfrecuencia)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                    if(empty($idfrecuencia) or !is_numeric($idfrecuencia)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        jsonResponse($response,400);
                        die();
                    }

                    $buscar_frecuencia = $this->model->getFrecuencia($idfrecuencia);
                    if(empty($buscar_frecuencia))
                    {
                        $response = array('status' => false , 'msg' => 'El registro no existe');
                    }else{

					    $response = array('status' => true , 'msg' => 'Datos encontrados', 'data' => $buscar_frecuencia);
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

        public function frecuencias()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "GET")
                {
                   $arrData = $this->model->getFrecuencias();
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

        public function registro()
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "POST")
                {
                    $_POST = json_decode(file_get_contents('php://input'),true);

                    if(empty($_POST['frecuencia']))
                    {
                        $response = array('status' => false , 'msg' => 'Frecuancia requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    $strFrecuencia = ucwords(strClean($_POST['frecuencia']));
                    $request = $this->model->setFrecuencia($strFrecuencia);
                    if($request > 0)
                    {
                        $arrFrecuencia = array('idFrecuencia' => $request,
                                            'frecuencia' => $strFrecuencia
                                            );
                        $response = array('status' => true , 'msg' => 'Datos guardados correctamente', 'data' => $arrFrecuencia);   
                    }else{
                        $response = array('status' => false , 'msg' => 'La frecuencia ya existe');
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

        public function actualizar($idfrecuencia)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "PUT")
                {
                    $arrData = json_decode(file_get_contents('php://input'),true);
                    if(empty($idfrecuencia) or !is_numeric($idfrecuencia)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        $code = 400;
                        jsonResponse($response,$code);
                        die();
                    }

                    if(empty($arrData['frecuencia']))
                    {
                        $response = array('status' => false , 'msg' => 'Frecuancia requerida');
                        jsonResponse($response,200);
                        die();
                    }

                    $strFrecuencia = ucwords(strClean($arrData['frecuencia']));
                    $buscar_frecuencia = $this->model->getFrecuencia($idfrecuencia);

                    if(empty($buscar_frecuencia))
                    {
                        $response = array('status' => false , 'msg' => 'El registro no existe');
                        jsonResponse($response,200);
                    }
                    
                    $request = $this->model->putFrecuencia($idfrecuencia,$strFrecuencia);
                    if($request)
                    {
                        $arrFrecuencia = array('idFrecuencia' => $idfrecuencia,
                                                'frecuencia' => $strFrecuencia
                                                );
					    $response = array('status' => true , 'msg' => 'Datos actualizados correctamente', 'data' => $arrFrecuencia);
                    }else{
                        $response = array('status' => false , 'msg' => 'El registro ya existe');
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

        public function eliminar($idfrecuencia)
        {
            try {
                $method = $_SERVER['REQUEST_METHOD'];
                $response = [];
                if($method == "DELETE")
                {
                    if(empty($idfrecuencia) or !is_numeric($idfrecuencia)){
                        $response = array('status' => false , 'msg' => 'Error en los parametros');
                        jsonResponse($response,400);
                        die();
                    }

                    $buscar_frecuencia = $this->model->getFrecuencia($idfrecuencia);
                    if(empty($buscar_frecuencia))
                    {
                        $response = array('status' => false , 'msg' => 'El registro no existe o ya fue eliminado');
                        jsonResponse($response,200);
                        die();
                    }

                    $request = $this->model->deleteFrecuencia($idfrecuencia);
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