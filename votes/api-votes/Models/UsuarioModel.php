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
							nombres_usuario,
							apellidos_usuario,
							email_usuario,
							DATE_FORMAT(datecreated_usuario, '%d-%m-%Y') as fechaRegistro
							FROM usuarios WHERE id_usuario = :iduser AND estado_usuario != :status ";
            $arrData = array(":iduser" => $this->intIdUsuario, ":status" => 0);
            $request = $this->select($sql,$arrData);
            return $request;
        }

        public function setUser(string $nombres, string $apellidos, string $email, string $password){
            $this->strNombres = $nombres;
			$this->strApellidos = $apellidos;
			$this->strEmail = $email;
			$this->strPassword = $password;

            $sql = "SELECT email FROM usuarios WHERE email = '$this->strEmail' AND status != 0";
            $request = $this->select_all($sql);
            if(empty($request))
            {
                $sql_insert = "INSERT INTO usuarios(nombre,apellido,email,password)
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

            $sql = "SELECT email FROM usuarios WHERE 
                    (email = :email AND id_usuario != :id ) AND
                    status != 0";
            $arrData = array(":email" => $this->strEmail,":id" => $this->intIdUsuario);
            $request_usuario = $this->select($sql,$arrData);
            if(empty($request_usuario))
            {
                if($this->strPassword == "")
                {
                    $sql = "UPDATE usuarios SET nombre = :nom, apellido = :ape, email = :email
                    WHERE id_usuario = :id ";
                    $arrData = array(":nom" => $this->strNombres,
                                    ":ape" =>  $this->strApellidos,
                                    ":email" => $this->strEmail,
                                    ":id" => $this->intIdUsuario);

                }else{
                    $sql = "UPDATE usuarios SET nombre = :nom, apellido = :ape, email = :email, password = :pass
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
							nombres_usuario,
							apellidos_usuario,
							email_usuario,
							DATE_FORMAT(datecreated_usuario, '%d-%m-%Y') as fechaRegistro
							FROM usuarios WHERE estado_usuario != 0 ORDER BY id_usuario DESC ";
            $request = $this->select_all($sql);
            return $request;
        }

        public function deleteUsuario($idusuario)
        {
            $this->intIdUsuario = $idusuario;
            $sql = "UPDATE usuarios SET status = :estado WHERE id_usuario = :id ";
            $arrData = array(":estado" => 0, ":id" => $this->intIdUsuario );
            $request = $this->update($sql,$arrData);
            return $request;
        }

        public function loginUser(string $email, string $password)
        {
            $this->strEmail = $email;
            $this->strPassword = $password;

            $sql = "SELECT id_usuario, status FROM usuarios WHERE
                        email = BINARY :email AND password = BINARY :pass AND status != 0 ";
            $arrData = array(":email" => $this->strEmail,
                                ":pass" => $this->strPassword );
            $request = $this->select($sql,$arrData);
            return $request;

        }

    }

?>