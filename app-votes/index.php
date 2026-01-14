<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, X-Auth-Token , Authorization ");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Allow: GET, POST, PUT, DELETE");

$configFile = "Config/Config.php";
if (file_exists("Config/Config.php")) {
    $configFile = "Config/Config.php";
} elseif (file_exists("config/Config.php")) {
    $configFile = "config/Config.php";
} elseif (file_exists("config/config.php")) {
    $configFile = "config/config.php";
}
require_once($configFile);
$helpersFile = "Helpers/Helpers.php";
if (file_exists("Helpers/Helpers.php")) {
    $helpersFile = "Helpers/Helpers.php";
} elseif (file_exists("helpers/Helpers.php")) {
    $helpersFile = "helpers/Helpers.php";
} elseif (file_exists("helpers/helpers.php")) {
    $helpersFile = "helpers/helpers.php";
}
require_once($helpersFile);
$currentHost = $_SERVER['HTTP_HOST'];
$isSubdomainAdmin = (strpos($currentHost, 'admin.') !== false);

// Default route logic
$defaultRoute = "home/home";
if ($isSubdomainAdmin) {
    $defaultRoute = "login/login";
}

$url = !empty($_GET['url']) ? $_GET['url'] : $defaultRoute;
$arrUrl = explode("/", $url);
$controller = $arrUrl[0];
$method = $arrUrl[0];
$params = "";

if (!empty($arrUrl[1])) {
    if ($arrUrl[1] != "") {
        $method = $arrUrl[1];
    }
}

if (!empty($arrUrl[2]) && $arrUrl[2] != "") {
    for ($i = 2; $i < count($arrUrl); $i++) {
        $params .= $arrUrl[$i] . ',';
    }
    $params = trim($params, ",");
}

require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");

?>