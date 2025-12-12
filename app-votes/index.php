<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, X-Auth-Token , Authorization ");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Allow: GET, POST, PUT, DELETE");

    require_once("Config/Config.php");
    require_once("Helpers/Helpers.php");
    $url = !empty($_GET['url']) ? $_GET['url'] : "home/home" ;
    $arrUrl = explode("/",$url);
    $controller = $arrUrl[0];
    $method =  $arrUrl[0];
    $params = "";

    if(!empty($arrUrl[1])){
        if($arrUrl[1] != ""){
            $method = $arrUrl[1]; 
        }
    }

    if(!empty($arrUrl[2]) && $arrUrl[2] != "")
    {
        for ($i=2; $i < count($arrUrl); $i++) { 
            $params .= $arrUrl[$i].',';
        }
        $params = trim($params,",");
    }

    require_once("Libraries/Core/Autoload.php");
    require_once("Libraries/Core/Load.php");

?>