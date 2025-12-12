<?php

    class FrecuenciaModel extends Mysql
    {
        private $intIdFrecuencia;
		private $strFrecuencia;
		private $strFecha;
		private $intStatus;

        public function __construct()
        {
            parent::__construct();
        }

        public function setFrecuencia(string $frecuencia)
        {
            $this->strFrecuencia = $frecuencia;

            $sql  = "SELECT * FROM frecuencia WHERE frecuencia = :frecuencia AND status != 0 ";
            $arrData = array(":frecuencia" => $this->strFrecuencia);
            $request = $this->select($sql,$arrData);

            if(empty($request))
            {
                $sql_insert = "INSERT INTO frecuencia(frecuencia) VALUES(:frecuencia)";
                $arrData = array(":frecuencia" => $this->strFrecuencia);
                $request_insert = $this->insert($sql_insert,$arrData);
                return $request_insert;
            }else{
                return false;
            }
        }

        public function getFrecuencia(int $idfrecuencia)
        {
           $this->intIdFrecuencia = $idfrecuencia; 
            $sql = "SELECT idfrecuencia,
							frecuencia,
							DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
							FROM frecuencia WHERE idfrecuencia = :idfrecuencia AND status != 0 "; 
            $arrData = array(":idfrecuencia" => $this->intIdFrecuencia);
			$request = $this->select($sql,$arrData);
            return $request;
        }

        public function getFrecuencias(){
            $sql = "SELECT idfrecuencia,
							frecuencia,
							DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
							FROM frecuencia WHERE status != 0 ORDER BY idfrecuencia DESC"; 
			$request = $this->select_all($sql);
            return $request;
        }
        
        public function putFrecuencia(int $idfrecuencia, string $frecuencia)
        {
            $this->intIdFrecuencia = $idfrecuencia; 
            $this->strFrecuencia = $frecuencia;
            $sql = "SELECT * FROM frecuencia WHERE 
                    (frecuencia = :fr AND idfrecuencia != :idfr) AND status != 0 ";
            $arrData = array(":fr" => $this->strFrecuencia,
                            ":idfr" => $this->intIdFrecuencia);
            $request = $this->select($sql,$arrData);
            
            if(empty($request))
            {
                $sql = "UPDATE frecuencia SET frecuencia = :fr WHERE idfrecuencia = :idfr";
                $arrData = array(":fr" => $this->strFrecuencia,
                            ":idfr" => $this->intIdFrecuencia);
                $request_update = $this->update($sql,$arrData);
                return $request_update;
            }else{
                return false;
            }
            
        }

        public function deleteFrecuencia(int $idfrecuencia)
        {
            $this->intIdFrecuencia = $idfrecuencia; 
            $sql = "UPDATE frecuencia SET status = :estado WHERE idfrecuencia = :idfr";
            $arrData = array(":estado" => 0,
                        ":idfr" => $this->intIdFrecuencia);
            $request_update = $this->update($sql,$arrData);
            return $request_update;
        }

    }

?>