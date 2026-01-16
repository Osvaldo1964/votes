<?php

class ResultadosModel extends Mysql
{
    private $intIdHead;
    private $intPlace;
    private $strFormulario;
    private $intUsuario;
    private $intIdBody;
    private $intCandidato;
    private $intVotos;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertHeadResultado(int $place, string $formulario, int $usuario)
    {
        // NOTA: $place aqui viene siendo el ID del Puesto o ID Mesa?? 
        // En el flujo anterior, el controlador mandaba un ID. 
        // Si el frontend manda ID PLACE (mesa antigua), debemos convertirlo o esperar que manden ID MESA nuevo.
        // Dado que el frontend NO ha cambiado, 'place' sigue siendo un ID de la tabla PLACES (Censo).
        // DEBEMOS TRADUCIRLO A ID_MESA.

        // 1. Obtener ID Mesa REAL desde el registro de Places
        $sqlInfo = "SELECT id_mesa_new FROM places WHERE id_place = $place";
        $infoPlace = $this->select($sqlInfo, array());

        if (empty($infoPlace) || empty($infoPlace['id_mesa_new'])) {
            return 0; // Mesa no migrada o no encontrada
        }

        $idMesa = $infoPlace['id_mesa_new'];

        // 2. Verificar estado actual de la Mesa
        $sqlMesa = "SELECT id_mesa, estado_mesa, formulario_mesa FROM mesas WHERE id_mesa = $idMesa";
        $mesaData = $this->select($sqlMesa, array());

        if (empty($mesaData))
            return 0;

        if ($mesaData['estado_mesa'] == 2 && !empty($mesaData['formulario_mesa'])) {
            return array('status' => 'duplicate', 'id' => $idMesa);
        }

        // 3. Actualizar la Mesa (Actua como el HeadResultado)
        $sqlUpdate = "UPDATE mesas 
                      SET formulario_mesa = ?, usuario_mesa = ?, fecha_mesa = NOW(), estado_mesa = 2 
                      WHERE id_mesa = ?";

        $arrUpdate = array($formulario, $usuario, $idMesa);
        $request = $this->update($sqlUpdate, $arrUpdate);

        return array('status' => 'inserted', 'id' => $idMesa); // Retornamos ID Mesa como ID Head
    }

    public function insertBodyResultado(int $headId, int $candidato, int $votos, int $usuario)
    {
        // $headId ahora es el ID DE LA MESA (id_mesa)
        $this->intIdHead = $headId;
        $this->intCandidato = $candidato;
        $this->intVotos = $votos;
        $this->intUsuario = $usuario;

        // Ajuste de nombres de columna segun captura del usuario (sufijo _body corto)
        $sql_insert = "INSERT INTO bodyresultado(id_mesa_body, candidato_body, votos_body, estado_body, creado_body, usuario_body) 
                       VALUES(?, ?, ?, ?, NOW(), ?)";
        $arrData = array(
            $this->intIdHead,
            $this->intCandidato,
            $this->intVotos,
            1,
            $this->intUsuario
        );
        $request_insert = $this->insert($sql_insert, $arrData);
        return $request_insert;
    }

    public function inicializarMesas(int $usuario)
    {
        // Ya no es necesario inicializar headresultados vacios.
        // Las mesas ya existen en la tabla `mesas`. 
        // Solo retornamos true o conteo.
        return true;
    }

    public function consultarEstadoMesa(int $place)
    {
        // Traducir ID Place -> ID Mesa
        $sqlInfo = "SELECT id_mesa_new FROM places WHERE id_place = $place";
        $infoPlace = $this->select($sqlInfo, array());

        if (empty($infoPlace) || empty($infoPlace['id_mesa_new']))
            return 0;

        $idMesa = $infoPlace['id_mesa_new'];

        $sql = "SELECT id_mesa, formulario_mesa, estado_mesa 
                FROM mesas 
                WHERE id_mesa = $idMesa AND estado_mesa = 2"; // Estado 2 = Informada

        $request = $this->select_all($sql);

        if (!empty($request)) {
            return $request[0];
        }
        return 0; // Libre
    }
}
