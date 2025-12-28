<?php

class Resultados extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
    }

    public function setE14()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method != "POST") {
                jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
                die();
            }

            // Validar Token y obtener ID Usuario
            $arrHeaders = getallheaders();
            $tokenData = fntAuthorization($arrHeaders);

            $idUsuario = 1; // Default
            if (isset($tokenData->id)) {
                $idUsuario = $tokenData->id;
            } elseif (isset($tokenData->data->id)) {
                $idUsuario = $tokenData->data->id;
            }

            // Obtener datos del POST
            if (empty($_POST['id_mesa']) || !isset($_POST['votos'])) {
                jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
                die();
            }

            $idMesa = intval($_POST['id_mesa']);
            $numeroFormulario = isset($_POST['numero_formulario']) ? strClean($_POST['numero_formulario']) : '';
            $strFormulario = 'E-14 ' . $numeroFormulario;
            $arrVotos = $_POST['votos'];

            // Intentar guardar Cabecera
            $requestHead = $this->model->insertHeadResultado($idMesa, $strFormulario, $idUsuario);

            // Manejo de Respuesta: Array = Duplicado o Éxito con Debug
            if (is_array($requestHead)) {

                // CASO 1: Éxito (Inserted o Updated)
                if (isset($requestHead['status'])) {
                    $idHead = $requestHead['id'];
                    $debugInfo = "Debug BD: Z=" . $requestHead['debug_zona'] . " P=" . $requestHead['debug_puesto'] . " M=" . $requestHead['debug_mesa'] .
                        " | Similares en BD: " . ($requestHead['debug_similar'] ?? "N/A");

                    $countVotos = 0;
                    if (is_array($arrVotos)) {
                        foreach ($arrVotos as $idCandidato => $votos) {
                            $votosFinal = intval($votos) >= 0 ? intval($votos) : 0;
                            $this->model->insertBodyResultado($idHead, $idCandidato, $votosFinal, $idUsuario);
                            $countVotos++;
                        }
                    }

                    if ($countVotos > 0) {
                        jsonResponse(['status' => true, 'msg' => "Resultados guardados correctamente. ($debugInfo)"], 200);
                    } else {
                        jsonResponse(['status' => true, 'msg' => "Cabecera guardada, pero sin votos. ($debugInfo)"], 200);
                    }
                } else {
                    // CASO 2: Duplicado Real (Array de BD sin 'status')
                    $formExistente = $requestHead['formulario_headresultado'];
                    jsonResponse(['status' => false, 'msg' => "¡Alerta! Esta mesa ya tiene un reporte registrado bajo el formulario: $formExistente."], 400);
                    die();
                }
            } else {
                // Caso legado o error (si retorna 0)
                jsonResponse(['status' => false, 'msg' => 'No se pudo guardar. Error desconocido.'], 500);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => 'Error del servidor: ' . $e->getMessage()], 500);
        }
        die();
    }

    public function verificarMesa()
    {
        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
            die();
        }

        $idMesa = intval($_POST['id_mesa']);
        if ($idMesa <= 0) {
            jsonResponse(['status' => false, 'msg' => 'ID Mesa inválido'], 400);
            die();
        }

        $estado = $this->model->consultarEstadoMesa($idMesa);

        if ($estado !== 0 && is_array($estado)) {
            // Ya existe
            $form = $estado['formulario_headresultado'];
            jsonResponse(['status' => false, 'msg' => "¡Atención! Ya existe un reporte registrado para esta mesa (Formulario $form)."], 200);
        } else {
            // Libre
            jsonResponse(['status' => true, 'msg' => 'Disponible'], 200);
        }
        die();
    }

    public function inicializar()
    {
        try {
            $arrHeaders = getallheaders();
            $tokenData = fntAuthorization($arrHeaders);
            $idUsuario = isset($tokenData->id) ? $tokenData->id : 1;

            $request = $this->model->inicializarMesas($idUsuario);

            jsonResponse(['status' => true, 'msg' => 'Proceso de Inicialización ejecutado. Mesas faltantes creadas.'], 200);
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => 'Error: ' . $e->getMessage()], 500);
        }
        die();
    }
}
