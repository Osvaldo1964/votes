<?php

    class ClienteModel extends Mysql
    {
        private $intIdCliente;
		private $strIdentificacion;
		private $strNombres;
		private $strApellidos;
		private $intTelefono;
		private $strEmail;
		private $strDireccion;
		private $strNit;
		private $strNomFiscal;
		private $strDirFiscal;
		private $intStatus;

        public function __construct()
        {
            parent::__construct();
        }

        public function setCliente(string $identificacion, string $nombres, string $apellidos, int $telefono, string $email, string $direccion, string $nit, string $nomfiscal, string $dirfiscal)
        {
            $this->strIdentificacion = $identificacion;
			$this->strNombres = $nombres;
			$this->strApellidos = $apellidos;
			$this->intTelefono = $telefono;
			$this->strEmail = $email;
			$this->strDireccion = $direccion;
			$this->strNit = $nit;
			$this->strNomFiscal = $nomfiscal;
			$this->strDirFiscal = $dirfiscal;
            //SELECT identificacion,email FROM cliente WHERE (email = 'info@abelosh.com' or identificacion = '242526') and status = 1;
            
            $sql = "SELECT identificacion,email FROM cliente WHERE (email = :email or identificacion = :ident) and status = :estado ";
            $arrParams = array(":email" => $this->strEmail,
                                ":ident" =>  $this->strIdentificacion,
                                ":estado" => 1
                            );
            $request = $this->select($sql,$arrParams);
            
            if(!empty($request))
            {
                return false;
            }else{
                $query_inset= "INSERT INTO cliente(identificacion,nombres,apellidos,telefono,email,direccion,nit,nombrefiscal,direccionfiscal)
                                VALUES(:ident,:nom,:ape,:tel,:email,:dir,:nit,:nomfiscal,:dirfiscal)";
                $arrData = array(":ident" =>  $this->strIdentificacion,
                                 ":nom" => $this->strNombres,
                                 ":ape" => $this->strApellidos,
                                 ":tel" => $this->intTelefono,
                                 ":email" => $this->strEmail,
                                 ":dir" => $this->strDireccion,
                                 ":nit" => $this->strNit,
                                 ":nomfiscal" => $this->strNomFiscal,
                                 ":dirfiscal" => $this->strDirFiscal
                            );
                $request_insert = $this->insert($query_inset,$arrData);
                return $request_insert;
            }

        }

        public function putCliente(int $idcliente, string $identificacion, string $nombres, string $apellidos, int $telefono, string $email, string $direccion, string $nit, string $nomfiscal, string $dirfiscal)
        {
            $this->intIdCliente = $idcliente;
            $this->strIdentificacion = $identificacion;
			$this->strNombres = $nombres;
			$this->strApellidos = $apellidos;
			$this->intTelefono = $telefono;
			$this->strEmail = $email;
			$this->strDireccion = $direccion;
			$this->strNit = $nit;
			$this->strNomFiscal = $nomfiscal;
			$this->strDirFiscal = $dirfiscal;

            $sql = "SELECT identificacion,email FROM cliente WHERE 
                    (email = :email AND idcliente != :id ) OR
                    (identificacion = :ident AND idcliente != :id) AND
                    status != 0";
            $arrData = array(":email" => $this->strEmail,
                             ":ident" => $this->strIdentificacion,
                             ":id" =>  $this->intIdCliente 
                            );
            $request_cliente = $this->select($sql,$arrData);

            if(empty($request_cliente))
            {
                $sql = "UPDATE cliente SET identificacion = :ident, nombres = :nom, apellidos = :ape, telefono = :tel, email = :email,
                                         direccion = :dir, nit = :nit, nombrefiscal = :nomfiscal, direccionfiscal = :dirfiscal
                        WHERE idcliente = :id ";
                $arrData = array(":ident" =>  $this->strIdentificacion,
                                 ":nom" => $this->strNombres,
                                 ":ape" => $this->strApellidos,
                                 ":tel" => $this->intTelefono,
                                 ":email" => $this->strEmail,
                                 ":dir" => $this->strDireccion,
                                 ":nit" => $this->strNit,
                                 ":nomfiscal" => $this->strNomFiscal,
                                 ":dirfiscal" => $this->strDirFiscal,
                                 ":id" => $this->intIdCliente
                            );
                $request = $this->update($sql,$arrData);
                return $request;

            }else{
                return false;
            }
        }

        public function getCliente(int $idcliente)
        {
            $this->intIdCliente = $idcliente;
            $sql = "SELECT idcliente,
                            identificacion,
                            nombres,
                            apellidos,
                            telefono,
                            email,
                            direccion,
                            nit,
                            nombreFiscal,
                            direccionFiscal,
                            DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
                            FROM cliente WHERE idcliente = :id AND status != 0";
            $arrData = array(":id" => $this->intIdCliente);
            $request = $this->select($sql,$arrData);
            return $request;
        }

        public function getClientes()
        {
            $sql = "SELECT idcliente,
                            identificacion,
                            nombres,
                            apellidos,
                            telefono,
                            email,
                            direccion,
                            nit,
                            nombreFiscal,
                            direccionFiscal,
                            DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
                            FROM cliente WHERE status != 0 ORDER BY idcliente DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function deleteCliente(int $idcliente)
        {
            /*
            $this->intIdCliente = $idcliente;
            $sql = "DELETE FROM cliente WHERE idcliente = :id ";
            $arrData = array(":id" => $this->intIdCliente );
            $request = $this->delete($sql,$arrData);
            return $request;
            */

            $this->intIdCliente = $idcliente;
            $sql = "UPDATE cliente SET status = :estado WHERE idcliente = :id ";
            $arrData = array(":estado" => 0, ":id" => $this->intIdCliente );
            $request = $this->update($sql,$arrData);
            return $request;

        }

    }

?>