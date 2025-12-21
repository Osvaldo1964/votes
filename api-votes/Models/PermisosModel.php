<?php

class PermisosModel extends Mysql
{
	public $intIdpermiso;
	public $intRolid;
	public $intModuloid;
	public $r;
	public $w;
	public $u;
	public $d;

	public function __construct()
	{
		parent::__construct();
	}

	public function selectModulos()
	{
		$sql = "SELECT * FROM modulos WHERE estado_modulo != 0";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectPermisosRol(int $idrol)
	{
		$this->intRolid = $idrol;
		$sql = "SELECT * FROM permisos WHERE rol_permiso = $this->intRolid";
		$request = $this->select_all($sql);
		return $request;
	}
	public function deletePermisos(int $idrol)
	{
		$this->intRolid = $idrol;
		$sql = "DELETE FROM permisos WHERE rol_permiso = ?";
		$arrData = array($this->intRolid);
		$request = $this->delete($sql, $arrData);
		return $request;
	}

	public function insertPermisos(int $idrol, int $idmodulo, int $r, int $w, int $u, int $d)
	{
		$this->intRolid = $idrol;
		$this->intModuloid = $idmodulo;
		$this->r = $r;
		$this->w = $w;
		$this->u = $u;
		$this->d = $d;
		$query_insert = "INSERT INTO permisos(rol_permiso,modulo_permiso,r_permiso,w_permiso,u_permiso,d_permiso) VALUES(?,?,?,?,?,?)";
		$arrData = array($this->intRolid, $this->intModuloid, $this->r, $this->w, $this->u, $this->d);
		$request_insert = $this->insert($query_insert, $arrData);
		return $request_insert;
	}

	public function permisosModulo(int $idrol)
	{
		$this->intRolid = $idrol;
		$sql = "SELECT p.id_permiso,
						   p.modulo_permiso,
						   m.titulo_modulo as modulo,
						   p.r_permiso,
						   p.w_permiso,
						   p.u_permiso,
						   p.d_permiso 
					FROM permisos p 
					INNER JOIN modulos m
					ON p.modulo_permiso = m.id_modulo
					WHERE p.rol_permiso = $this->intRolid";
		$request = $this->select_all($sql);
		$arrPermisos = array();
		for ($i = 0; $i < count($request); $i++) {
			$arrPermisos[$request[$i]['modulo_permiso']] = $request[$i];
		}
		return $arrPermisos;
	}
}
