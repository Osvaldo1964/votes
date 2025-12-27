<?php
class Home extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function home()
    {
        // Si no esta logueado -> Login
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
        } else {
            // Si esta logueado -> Dashboard
            header('Location: ' . base_url() . '/dashboard');
        }
        die();
    }
}
