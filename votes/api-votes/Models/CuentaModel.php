<?php

    class CuentaModel extends Mysql
    {
        private $intIdCuenta;
		private $intIdCliente;
		private $intIdProducto;
		private $intIdFrecuencia;
		private $intMonto;
		private $intCuotas;
		private $intMontoCuotas;
		private $intCargo;
		private $intSaldo;

        public function __construct()
        {
            parent::__construct();
        }

        public function setCuenta(int $idcliente, int $idproducto, int $idfrecuencia, float $monto, int $cuotas, float $montocuotas, float $cargo, float $saldo)
        {
            $this->intIdCliente = $idcliente;
			$this->intIdProducto = $idproducto;
			$this->intIdFrecuencia = $idfrecuencia;
			$this->intMonto = $monto;
			$this->intCuotas = $cuotas;
			$this->intMontoCuotas = $montocuotas;
			$this->intCargo = $cargo;
			$this->intSaldo = $saldo;

            $sql = "INSERT INTO cuenta(clienteid,productoid,frecuenciaid,monto,cuotas,monto_cuotas,cargo,saldo) 
                        VALUES (:idcl,:idpr,:idfr,:monto,:cuotas,:mtcuotas,:cargo,:saldo)";
            $arrData = array(":idcl" => $this->intIdCliente,
                             ":idpr" => $this->intIdProducto,
                             ":idfr" => $this->intIdFrecuencia,
                             ":monto" => $this->intMonto,
                             ":cuotas" => $this->intCuotas,
                             ":mtcuotas" => $this->intMontoCuotas,
                             ":cargo" => $this->intCargo,
                             ":saldo" => $this->intSaldo
                            );
            $reuquest_insert = $this->insert($sql,$arrData);
            return $reuquest_insert;
        }

        public function getCuenta(int $idcuenta)
        {
            $this->intIdCuenta = $idcuenta;
            $sql = "SELECT c.idcuenta, c.frecuenciaid, f.frecuencia, c.monto, c.cuotas, c.monto_cuotas, c.cargo, c.saldo,
                            DATE_FORMAT(c.datecreated, '%d-%m-%Y') as fechaRegistro,
                            c.clienteid, cl.nombres, cl.apellidos, cl.telefono, cl.email, cl.direccion, cl.nit, cl.nombrefiscal,
                            cl.direccionfiscal,
                            c.productoid, p.codigo as cod_producto, p.nombre
                            FROM cuenta c 
                            INNER JOIN frecuencia f
                            ON c.frecuenciaid = f.idfrecuencia
                            INNER JOIN cliente cl
                            ON c.clienteid = cl.idcliente
                            INNER JOIN producto p
                            ON c.productoid = p.idproducto
                            WHERE c.idcuenta  = :idcuenta ";
            $arrData = array(":idcuenta" => $this->intIdCuenta);
            $request = $this->select($sql,$arrData);
            return $request;

        }

        public function getMovimientos(int $idcuenta)
        {
            $this->intIdCuenta = $idcuenta;
            $sql = "SELECT m.idmovimiento, m.monto, m.descripcion, DATE_FORMAT(m.datecreated, '%d-%m-%Y') as fecha,
                    tm.idtipomovimiento, tm.movimiento, tm.tipo_movimiento
                    FROM movimiento m 
                    INNER JOIN tipo_movimiento tm
                    ON m.tipomovimientoid = tm.idtipomovimiento
                    WHERE m.cuentaid = $this->intIdCuenta AND m.status != 0 ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function getCuentas()
        {
            $sql = "SELECT c.idcuenta,
                            DATE_FORMAT(c.datecreated, '%d-%m-%Y') as fechaRegistro,
                            concat(cl.nombres,' ',cl.apellidos) as cliente,
                            f.frecuencia,
                            c.cuotas, c.monto_cuotas,
                            c.cargo, c.saldo
                            FROM cuenta c 
                            INNER JOIN frecuencia f
                            ON c.frecuenciaid = f.idfrecuencia
                            INNER JOIN cliente cl
                            ON c.clienteid = cl.idcliente
                            WHERE c.status != 0 ORDER BY c.idcuenta DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

    }

?>