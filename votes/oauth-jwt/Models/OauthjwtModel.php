<?php

    class OauthjwtModel extends Mysql
    {
        private $intIdCliente;
        private $strNombres;
		private $strApellidos;
		private $strEmail;
		private $strPassword;

        private $intScopeId;
        private $strScope;
        private $strClientId;
        private $strKeySecret;
        private $strToken;
        private $strEpires;

        public function __construct()
        {
            parent::__construct();
        }

        public function setCliente(string $nombres, string $apellidos, string $email, string $password  ){
            $this->strNombres = $nombres;
			$this->strApellidos = $apellidos;
			$this->strEmail = $email;
			$this->strPassword = $password;

            $sql = "SELECT email FROM cliente_jwt WHERE email = '$this->strEmail' AND status != 0";
            $request = $this->select_all($sql);
            if(empty($request))
            {
                $sql_insert = "INSERT INTO cliente_jwt(nombres,apellidos,email,password)
                                VALUES(:nom,:ape,:email,:pass)";
                $arrData = array(":nom" => $this->strNombres,
                                ":ape" => $this->strApellidos,
                                ":email" => $this->strEmail,
                                ":pass" => $this->strPassword);
                $request_insert = $this->insert($sql_insert,$arrData);
                return $request_insert;
            }else{
                return false;
            }
        }

        public function getCliente(string $email){
            $this->strEmail = $email;
            $sql = "SELECT id_clientejwt,nombres,apellidos,email FROM cliente_jwt WHERE email = :email AND status != :status ";
            $arrData = array(":email" => $this->strEmail, ":status" => 0);
            $request = $this->select($sql,$arrData);
            return $request;
        }

        public function setScope(string $scope, string $cliente_id, string $key_secret, string $clientejwt_id){
            $this->strScope = $scope;
			$this->strClientId = $cliente_id;
			$this->strKeySecret = $key_secret;
			$this->intIdCliente = $clientejwt_id;

            $sql = "SELECT * FROM scope_jwt WHERE scope = '$this->strScope'
                    AND clientejwt_id = '$this->intIdCliente' AND status != 0";
            $request = $this->select_all($sql);
            if(count($request) > 0){
                return false;
            }else{
                $sql_insert = "INSERT INTO scope_jwt(scope,client_id,key_secret,clientejwt_id)
                                VALUES(:scope,:clid,:ksecret,:clienteid)";
                $arrData = array(":scope" => $this->strScope,
                                ":clid" => $this->strClientId,
                                ":ksecret" => $this->strKeySecret,
                                ":clienteid" => $this->intIdCliente);
                $request_insert = $this->insert($sql_insert,$arrData);
                return $request_insert;
            }

        }

        public function getScopeAuth(string $client_id, string $key_secret){
            $this->strKeySecret = $key_secret;
			$this->strClientId = $client_id;
            
            $sql = "SELECT s.id_scope,s.scope,c.id_clientejwt,c.email 
                    FROM scope_jwt s
                    INNER JOIN cliente_jwt c
                    ON s.clientejwt_id = c.id_clientejwt 
                    WHERE s.client_id = BINARY :clid AND s.key_secret = BINARY :ksecret 
                    AND s.status != :status AND c.status != :status ";
            $arrData = array(":clid" => $this->strClientId,
                            ":ksecret" => $this->strKeySecret,
                            ":status" => 0);
            $request = $this->select($sql,$arrData);
            return $request;
        }

        public function setTokenDB(int $clientejwt_id, int $scope_id, string $token, string $expiration ){
            $this->intIdCliente = $clientejwt_id;
            $this->intScopeId = $scope_id;
            $this->strToken = $token;
            $this->strEpires = $expiration;

            $sql_insert = "INSERT INTO token_jwt(clientejwt_id,scope_id,access_token,expirres_in)
            VALUES(:clid,:scopeid,:token,:expires)";
            $arrData = array(":clid" => $this->intIdCliente,
                        ":scopeid" => $this->intScopeId,
                        ":token" => $this->strToken,
                        ":expires" => $this->strEpires);
            $request_insert = $this->insert($sql_insert,$arrData);
            return $request_insert;
        }

        public function getToken(string $token){
            $this->strToken = $token;
            $sql = "SELECT t.access_token, t.expirres_in, s.key_secret
                    FROM token_jwt t 
                    INNER JOIN scope_jwt s
                    ON t.scope_id = s.id_scope
                    WHERE t.access_token = BINARY :token
                    AND t.status != 0 AND s.status != 0 ";
            $arrData = array(":token" => $this->strToken);
            $request = $this->select($sql,$arrData);
            return $request;
        }
        
        

    }

?>