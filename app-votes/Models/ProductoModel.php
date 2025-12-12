<?php

    class ProductoModel extends Mysql
    {
        private $intIdProducto;
		private $strCodigo;
		private $strNombre;
		private $strDescripcion;
		private $strPrecio;
		private $intStatus;

        public function __construct()
        {
            parent::__construct();
        }

        public function setProducto(string $codigo, string $nombre, string $descripcion, string $precio)
        {
            $this->strCodigo = $codigo;
			$this->strNombre = $nombre;
			$this->strDescripcion = $descripcion;
			$this->strPrecio = $precio;

            $sql = "SELECT * FROM producto WHERE codigo = :cod AND status = 1";
            $arrData = array(":cod" => $this->strCodigo);
            $producto = $this->select($sql,$arrData);

            if(empty($producto))
            {
                $query_insert = "INSERT INTO producto(codigo,nombre,descripcion,precio) 
                                VALUES (:cod,:nom,:desc,:precio)";
                $arrData = array(":cod" => $this->strCodigo,
                                  ":nom" => $this->strNombre,
                                  ":desc" => $this->strDescripcion,
                                  ":precio" => $this->strPrecio
                                );
                $request = $this->insert($query_insert,$arrData);
                return $request;
            }else{
                return false;
            }
            
        }

        public function getProducto(int $idproducto)
        {
           $this->intIdProducto = $idproducto; 
           $sql = "SELECT idproducto,codigo,nombre,descripcion,precio,
                    DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
                     FROM producto WHERE idproducto = :id AND status != 0";
           $arrData = array(":id" => $this->intIdProducto);
           $request = $this->select($sql,$arrData);
           return $request;
        }

        public function getProductos()
        {
            $sql = "SELECT idproducto,codigo,nombre,descripcion,precio,
                    DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
                    FROM producto WHERE status != 0 ORDER BY idproducto DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function putProducto(int $idproducto, string $codigo, string $nombre, string $descripcion, string $precio)
        {
            $this->intIdProducto = $idproducto; 
            $this->strCodigo = $codigo;
			$this->strNombre = $nombre;
			$this->strDescripcion = $descripcion;
			$this->strPrecio = $precio;

            $this->intIdProducto = $idproducto; 
            $sql = "SELECT * FROM producto WHERE (codigo = :cod AND idproducto != :id) AND status != 0";
            $arrData = array(":cod" => $this->strCodigo, ":id" => $this->intIdProducto);
            $request = $this->select($sql,$arrData);

            if(empty($request))
            {
                $sql = "UPDATE producto SET codigo=:cod, nombre=:nom, descripcion=:desc, precio=:precio
                        WHERE idproducto = :id ";
                $arrData = array(":cod" => $this->strCodigo,
                                ":nom" => $this->strNombre,
                                ":desc" => $this->strDescripcion,
                                ":precio" => $this->strPrecio,
                                ":id" => $this->intIdProducto
                            );
                $request_update = $this->update($sql,$arrData);
                return $request_update;
            }else{
                return false;
            }

        }

        public function deleteProducto(int $idproducto)
        {
            $this->intIdProducto = $idproducto;
            $sql = "UPDATE producto SET status = :estado WHERE idproducto = :id ";
            $arrData = array(":estado" => 0, ":id" => $this->intIdProducto);
            $request_update = $this->update($sql, $arrData);
            return $request_update;
        }


        
    }

?>