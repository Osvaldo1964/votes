<?php
class Logout extends Controllers
{
	public function __construct()
	{
		// Iniciamos la sesión para poder destruirla
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	public function logout()
	{
		// 1. Vaciamos el array de sesión
		session_unset();

		// 2. Destruimos la sesión en el servidor
		session_destroy();

		// 3. Redirigimos al login, pero agregamos un parámetro 
		// para decirle al JS que también limpie el LocalStorage
		header('Location: ' . base_url() . '/login?logout=true');
		die();
	}
}