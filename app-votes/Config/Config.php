<?php
const BASE_URL = "http://app-votes.com/";
const API_OAUTH_JWT = "http://ovcauth-ovcsystems.com/oauthjwt/token";
const API_VALID_JWT = "http://ovcauth-ovcsystems.com/oauthjwt/tokenValidate/";
const BASE_URL_API = "http://localhost/votes/api-votes/";

//Zona horaria
date_default_timezone_set("America/Bogota");

//Datos de conexión a Base de Datos
const CONNECTION = true;
const DB_HOST = "localhost";
const DB_NAME = "db-votes";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "utf8";

// Scope Sistema ventas
const CLIENT_JWT = 9;
const CLIENT_ID = "6cbcb6d052547e5d560e8b8bf55ea170a0ade8e96f0534d67be256c8a617204c-08d4a98d34bec82c9c12b022b4cc2a611df2eda9da2f25f85f386197bbd9321f";
const KEY_SECRET = "08d4a98d34bec82c9c12b022b4cc2a611df2eda9da2f25f85f386197bbd9321f-6cbcb6d052547e5d560e8b8bf55ea170a0ade8e96f0534d67be256c8a617204c";

//Deliminadores decimal y millar Ej. 24,1989.00
const SPD = "."; //Separador decimal
const SPM = ","; //Separador millar

//Simbolo de moneda
const SMONEY = "$";

// ReCAPTCHA v3 Keys
const RECAPTCHA_SITE_KEY = "6Le2ikQsAAAAAJVqo_KpOrqhzdwpmLbM-vSTwjVh"; // Pega aquí tu Site Key
const RECAPTCHA_SECRET_KEY = "6Le2ikQsAAAAAHPJkiMyCOQwhOGaEGKMdsNXMyMN"; // Pega aquí tu Secret Key
