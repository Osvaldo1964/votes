<?php
class ResultadosModel extends Mysql
{
    private $intIdHead;
    private $intCandidato;
    private $intVotos;
    private $intUsuario;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertHeadResultado(int $place, string $formulario, int $usuario)
    {
        // 1. Obtener ID Mesa REAL
        $sqlInfo = "SELECT id_mesa_new FROM places WHERE id_place = $place";
        $infoPlace = $this->select($sqlInfo, array());

        if (empty($infoPlace) || empty($infoPlace['id_mesa_new'])) {
            return 0; 
        }

        $idMesa = $infoPlace['id_mesa_new'];

        // 2. Verificar duplicados
        $sqlMesa = "SELECT id_mesa, estado_mesa, formulario_mesa FROM mesas WHERE id_mesa = $idMesa";
        $mesaData = $this->select($sqlMesa, array());

        if (empty($mesaData)) return 0;

        if ($mesaData['estado_mesa'] == 2 && !empty($mesaData['formulario_mesa'])) {
            // Retornamos array con info duplicada
            return array('status' => 'duplicate', 'formulario' => $mesaData['formulario_mesa']);
        }

        // 3. Actualizar Mesa (Head)
        $sqlUpdate = "UPDATE mesas SET formulario_mesa = ?, usuario_mesa = ?, fecha_mesa = NOW(), estado_mesa = 2 WHERE id_mesa = ?";
        $arrUpdate = array($formulario, $usuario, $idMesa);
        $this->update($sqlUpdate, $arrUpdate);

        // Retornamos éxito limpio
        return array('status' => 'inserted', 'id' => $idMesa);
    }

    public function insertBodyResultado(int $headId, int $candidato, int $votos, int $usuario)
    {
        $this->intIdHead = $headId;
        $this->intCandidato = $candidato;
        $this->intVotos = $votos;
        $this->intUsuario = $usuario;

        $sql_insert = "INSERT INTO bodyresultado(id_mesa_body, candidato_body, votos_body, estado_body, creado_body, usuario_body) VALUES(?, ?, ?, ?, NOW(), ?)";
        $arrData = array($this->intIdHead, $this->intCandidato, $this->intVotos, 1, $this->intUsuario);
        return $this->insert($sql_insert, $arrData);
    }
    
    public function consultarEstadoMesa(int $place)
    {
        $sqlInfo = "SELECT id_mesa_new FROM places WHERE id_place = $place";
        $infoPlace = $this->select($sqlInfo, array());
        if (empty($infoPlace) || empty($infoPlace['id_mesa_new'])) return 0;
        
        $idMesa = $infoPlace['id_mesa_new'];
        $sql = "SELECT id_mesa, formulario_mesa, estado_mesa FROM mesas WHERE id_mesa = $idMesa AND estado_mesa = 2";
        $request = $this->select($sql); 
        return !empty($request) ? $request : 0;
    }

    public function inicializarMesas(int $usuario)
    {
        return true;
    }
}
?>
