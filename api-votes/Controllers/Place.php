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

