<?php

    class MovimientoModel extends Mysql
    {
        private $intIdTipoMovimiento;
        private $strMovimiento;
        private $intTipoMovimiento;
        private $strDescTipoMovimiento;
		
        private $intIdMovimiento;
        private $intCuendaID;
		private $descripcion;
		private $intMonto;
		private $strFecha;

        public function __construct()
        {
            parent::__construct();
        }

        public function setTipoMovimiento(string $movimiento, int $tipomovimiento, string $descripcion)
        {
            $this->strMovimiento = $movimiento;
            $this->intTipoMovimiento = $tipomovimiento;
            $this->strDescTipoMovimiento = $descripcion;

            $sql = "SELECT * FROM tipo_movimiento WHERE movimiento = :mov AND status != 0 ";
            $arrData = array(":mov" => $this->strMovimiento);
            $request = $this->select($sql,$arrData);
            if(empty($request))
            {
                $sql_insert = "INSERT INTO tipo_movimiento(movimiento,tipo_movimiento,descripcion)
                                VALUES(:mov,:tipo_mov,:desc)";
                $arrData = array(":mov" => $this->strMovimiento,
                                 ":tipo_mov" =>  $this->intTipoMovimiento,
                                 ":desc" => $this->strDescTipoMovimiento
                                );
                $request_insert = $this->insert($sql_insert,$arrData);
                return $request_insert;
            }else{
                return false;
            }
        }

        public function getTiposMovimiento()
        {
            $sql = "SELECT idtipomovimiento,movimiento,tipo_movimiento 
                    FROM tipo_movimiento WHERE status !=0  ORDER BY idtipomovimiento DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function setMovimiento(int $idcuenta, int $idmovimiento, int $movimiento, float $monto, string $descripcion)
        {
            $this->intCuendaID = $idcuenta;
            $this->intIdMovimiento = $idmovimiento;
            $this->intTipoMovimiento = $movimiento;
            $this->intMonto = $monto;
            $this->descripcion = $descripcion;
            $sql = "INSERT INTO movimiento(cuentaid,tipomovimientoid,movimiento,monto,descripcion)
                            VALUES(:idcuenta,:tpmovimiento,:movimiento,:monto,:descripcion)";
            $arrData = array(":idcuenta" => $this->intCuendaID,
                                ":tpmovimiento" => $this->intIdMovimiento,
                                ":movimiento" => $this->intTipoMovimiento,
                                ":monto" => $this->intMonto,
                                ":descripcion" => $this->descripcion
                            );
            $request_insert = $this->insert($sql,$arrData);
            return $request_insert;
        }

        public function getMovimiento(int $idmovimiento)
        {
            $this->intIdMovimiento = $idmovimiento;
            $sql = "SELECT m.idmovimiento, m.cuentaid, m.movimiento, m.monto, m.descripcion, 
                        DATE_FORMAT(m.datecreated, '%d-%m-%Y') as fecha,
                        tm.idtipomovimiento, tm.movimiento as nombreMovimiento 
                        FROM movimiento m
                        INNER JOIN tipo_movimiento tm
                        ON m.tipomovimientoid = tm.idtipomovimiento
                        WHERE m.idmovimiento = :idmovimiento AND m.status !=0 ";
            $arrData = array(":idmovimiento" => $this->intIdMovimiento); 
            $request = $this->select($sql,$arrData);
            return $request;
        }

        public function getMovimientos()
        {
            $sql = "SELECT m.idmovimiento, m.cuentaid, m.monto, 
                        DATE_FORMAT(m.datecreated, '%d-%m-%Y') as fecha,
                        tm.movimiento as nombreMovimiento 
                        FROM movimiento m
                        INNER JOIN tipo_movimiento tm
                        ON m.tipomovimientoid = tm.idtipomovimiento
                        WHERE m.status !=0 ORDER BY m.idmovimiento DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function anularMovimiento(int $idmovimiento)
        {
            $this->intIdMovimiento = $idmovimiento;
            $sql = "CALL anular_movimiento(:idmovimiento)";
            $arrData = array(":idmovimiento" => $this->intIdMovimiento);
            $request = $this->call_execute($sql,$arrData);
            return $request;
        }
    }

?>