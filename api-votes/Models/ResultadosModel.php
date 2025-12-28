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
        $this->intPlace = $place;
        $this->strFormulario = $formulario;
        $this->intUsuario = $usuario;
        $return = 0;

        // 1. Obtener datos geográficos del Lugar que intentamos registrar
        $sqlInfo = "SELECT idzona_place, nameplace_place, mesa_place FROM places WHERE id_place = $this->intPlace";
        $infoPlace = $this->select($sqlInfo, array());

        if (empty($infoPlace)) {
            return 0;
        }

        $zona = $infoPlace['idzona_place'];
        $puesto = addslashes($infoPlace['nameplace_place']); // Escapamos comillas por si el nombre tiene (' )
        $mesa = $infoPlace['mesa_place'];

        // 2. Validar si ya existe ALGÚN reporte con esa misma combinación geográfica
        // Interpolación directa para descartar fallos de binding
        $sql = "SELECT h.id_headresultado, h.formulario_headresultado
                FROM headresultado h
                INNER JOIN places p ON h.place_headresultado = p.id_place
                WHERE p.idzona_place = $zona
                AND TRIM(p.nameplace_place) = TRIM('$puesto') 
                AND CAST(p.mesa_place AS UNSIGNED) = CAST('$mesa' AS UNSIGNED)
                AND h.estado_headresultado != 0";

        $request = $this->select_all($sql);

        if (!empty($request)) {
            // Ya existe un registro. Verificamos si es un PENDIENTE o un REAL.
            $registro = $request[0];

            if ($registro['formulario_headresultado'] === 'PENDIENTE') {
                // Es una mesa inicializada esperando datos -> HACEMOS UPDATE
                $idHead = $registro['id_headresultado'];
                $sqlUpdate = "UPDATE headresultado 
                               SET formulario_headresultado = ?, usuario_headresultado = ?, creado_headresultado = NOW()
                               WHERE id_headresultado = $idHead";
                $arrUpdate = array($this->strFormulario, $this->intUsuario);
                $this->update($sqlUpdate, $arrUpdate);

                return array('status' => 'updated', 'id' => $idHead, 'debug_zona' => $zona, 'debug_puesto' => $puesto, 'debug_mesa' => $mesa);
            } else {
                // Es una mesa con datos reales -> ES UN DUPLICADO (Array)
                return $registro;
            }
        }

        // DIAGNÓSTICO: Si llegamos aquí, es que NO encontró coincidencia exacta.
        // Buscamos qué puestos SÍ existen para esa Zona y Mesa, para ver la diferencia de nombres.
        $sqlDiag = "SELECT DISTINCT p.nameplace_place 
                    FROM headresultado h
                    INNER JOIN places p ON h.place_headresultado = p.id_place
                    WHERE p.idzona_place = $zona 
                    AND CAST(p.mesa_place AS UNSIGNED) = CAST('$mesa' AS UNSIGNED)
                    AND h.estado_headresultado != 0";
        $diagRequest = $this->select_all($sqlDiag);
        $puestosEncontrados = "Ninguno";
        if (!empty($diagRequest)) {
            $names = [];
            foreach ($diagRequest as $row) {
                $names[] = "'" . $row['nameplace_place'] . "'";
            }
            $puestosEncontrados = implode(", ", $names);
        }

        // 3. Si no existe nada (ni pendiente ni real), insertamos nuevo (Fallback)
        $sql_insert = "INSERT INTO headresultado(place_headresultado, formulario_headresultado, creado_headresultado, estado_headresultado, usuario_headresultado) 
                       VALUES(?, ?, NOW(), ?, ?)";
        $arrData = array(
            $this->intPlace,
            $this->strFormulario,
            1,
            $this->intUsuario
        );
        $request_insert = $this->insert($sql_insert, $arrData);

        // DEBUG: Enviamos lo que buscamos VS lo que encontramos similar
        return array(
            'status' => 'inserted',
            'id' => $request_insert,
            'debug_zona' => $zona,
            'debug_puesto' => $puesto,
            'debug_mesa' => $mesa,
            'debug_similar' => $puestosEncontrados
        );
    }

    public function insertBodyResultado(int $headId, int $candidato, int $votos, int $usuario)
    {
        $this->intIdHead = $headId;
        $this->intCandidato = $candidato;
        $this->intVotos = $votos;
        $this->intUsuario = $usuario;

        $sql_insert = "INSERT INTO bodyresultado(head_bodyresultado, candidato_bodyresultado, votos_bodyresultado, estado_bodyresultado, creado_bodyresultado, usuario_bodyresultado) 
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
        // Corregido: Insertar 'PENDIENTE' como formulario_headresultado
        $sql = "INSERT INTO headresultado (place_headresultado, formulario_headresultado, creado_headresultado, estado_headresultado, usuario_headresultado)
                SELECT * FROM (
                    SELECT MAX(id_place) as id_rep, 'PENDIENTE' as form, NOW() as fecha, 1 as estado, $usuario as user
                    FROM places
                    GROUP BY idzona_place, nameplace_place, mesa_place
                ) as MesasUnicas
                WHERE NOT EXISTS (
                    SELECT 1 FROM headresultado h
                    INNER JOIN places p ON h.place_headresultado = p.id_place
                    INNER JOIN places p_new ON p_new.id_place = MesasUnicas.id_rep
                    WHERE p.idzona_place = p_new.idzona_place
                    AND p.nameplace_place = p_new.nameplace_place
                    AND CAST(p.mesa_place AS UNSIGNED) = CAST(p_new.mesa_place AS UNSIGNED)
                )";

        $request = $this->insert($sql, array());
        return $request;
    }
    public function consultarEstadoMesa(int $place)
    {
        $this->intPlace = $place;
        $sqlInfo = "SELECT idzona_place, nameplace_place, mesa_place FROM places WHERE id_place = $this->intPlace";
        $infoPlace = $this->select($sqlInfo, array());

        if (empty($infoPlace)) return 0;

        $zona = $infoPlace['idzona_place'];
        $puesto = addslashes($infoPlace['nameplace_place']);
        $mesa = $infoPlace['mesa_place'];

        $sql = "SELECT h.id_headresultado, h.formulario_headresultado, h.estado_headresultado
                FROM headresultado h
                INNER JOIN places p ON h.place_headresultado = p.id_place
                WHERE p.idzona_place = $zona
                AND TRIM(p.nameplace_place) = TRIM('$puesto') 
                AND CAST(p.mesa_place AS UNSIGNED) = CAST('$mesa' AS UNSIGNED)
                AND h.estado_headresultado != 0";

        $request = $this->select_all($sql);

        if (!empty($request)) {
            $row = $request[0];
            // Si es PENDIENTE, se considera "LIBRE" para efectos de cargar (porque hay que llenarlo).
            if ($row['formulario_headresultado'] === 'PENDIENTE') {
                return 0; // Disponible para digitar
            }
            return $row; // Ocupado / Ya registrado
        }
        return 0; // Libre
    }
}
