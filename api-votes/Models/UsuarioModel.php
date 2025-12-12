<?php

    class UsuarioModel extends Mysql
    {
        private $intIdUsuario;
		private $strNombres;
		private $strApellidos;
		private $strEmail;
		private $strPassword;

        public function __construct()
        {
            parent::__construct();
        }

        public function getUsuario(int $idusuario)
        {
            $this->intIdUsuario = $idusuario;
            $sql = "SELECT id_usuario,
							nombre,
							apellido,
							email,
							DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
							FROM usuario WHERE id_usuario = :iduser AND status != :status ";
            $arrData = array(":iduser" => $this->intIdUsuario, ":status" => 0);
            $request = $this->select($sql,$arrData);
            return $request;
        }

        public function setUser(string $nombres, string $apellidos, string $email, string $password){
            $this->strNombres = $nombres;
			$this->strApellidos = $apellidos;
			$this->strEmail = $email;
			$this->strPassword = $password;

            $sql = "SELECT email_usuario FROM usuarios WHERE email_usuario = '$this->strEmail' AND estado_usuario != 0";
            $request = $this->select_all($sql);
            if(empty($request))
            {
                $sql_insert = "INSERT INTO usuarios(nombres_usuario,apellidos_usuario,email_usuario,password_usuario)
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

        public function putUser(int $idusuario, string $nombres, string $apellidos, string $email, string $password){
            $this->intIdUsuario = $idusuario;
            $this->strNombres = $nombres;
			$this->strApellidos = $apellidos;
			$this->strEmail = $email;
			$this->strPassword = $password;

            $sql = "SELECT email FROM usuario WHERE 
                    (email = :email AND id_usuario != :id ) AND
                    status != 0";
            $arrData = array(":email" => $this->strEmail,":id" => $this->intIdUsuario);
            $request_usuario = $this->select($sql,$arrData);
            if(empty($request_usuario))
            {
                if($this->strPassword == "")
                {
                    $sql = "UPDATE usuario SET nombre = :nom, apellido = :ape, email = :email
                    WHERE id_usuario = :id ";
                    $arrData = array(":nom" => $this->strNombres,
                                    ":ape" =>  $this->strApellidos,
                                    ":email" => $this->strEmail,
                                    ":id" => $this->intIdUsuario);

                }else{
                    $sql = "UPDATE usuario SET nombre = :nom, apellido = :ape, email = :email, password = :pass
                    WHERE id_usuario = :id ";
                    $arrData = array(":nom" => $this->strNombres,
                                    ":ape" =>  $this->strApellidos,
                                    ":email" => $this->strEmail,
                                    ":pass" => $this->strPassword,
                                    ":id" => $this->intIdUsuario);
                }
                $request = $this->update($sql,$arrData);
                return $request;
            }else{
                return false;
            }

        }

        public function getUsuarios()
        {
            $sql = "SELECT id_usuario,
							nombre,
							apellido,
							email,
							DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro
							FROM usuario WHERE status != 0 ORDER BY id_usuario DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function deleteUsuario($idusuario)
        {
            $this->intIdUsuario = $idusuario;
            $sql = "UPDATE usuario SET status = :estado WHERE id_usuario = :id ";
            $arrData = array(":estado" => 0, ":id" => $this->intIdUsuario );
            $request = $this->update($sql,$arrData);
            return $request;
        }

        public function loginUser(string $email, string $password)
        {
            $this->strEmail = $email;
            $this->strPassword = $password;

            $sql = "SELECT id_usuario, estado_usuario FROM usuarios WHERE
                        email_usuario = BINARY :email AND password_usuario = BINARY :pass AND estado_usuario != 0 ";
            $arrData = array(":email" => $this->strEmail,
                                ":pass" => $this->strPassword );
            $request = $this->select($sql,$arrData);
            return $request;

        }

    }

?>