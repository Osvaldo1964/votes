<?php
// 1. EL TOKEN QUE SABEMOS QUE ES VÁLIDO (Pégalo aquí de nuevo para asegurar)
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6NCwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1hQGVtcHJlc2ExLmNvbSIsImlhdCI6MTc2NTc2MjA0MSwiZXhwIjoxNzY1ODQ4NDQxfQ.6bs_F3ndV6-Mo5rcFs_Ni8Og39SzVuv8nFUyjrQ5Htz-Ptv3r9RPGhDCOqscgnrBq-_AwCkvCDQVaYzUAisiuA'; 

// 2. CONFIGURACIÓN DE MÁSCARA
$url = 'http://api-votes.com/roles/getRoles'; // ¡No olvides poner la ruta correcta!

$headers = [
    'Host: api-votes.com',
    'Connection: keep-alive',
    'Accept: application/json, text/javascript, */*; q=0.01',
    'Authorization: Bearer ' . $token,
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36',
    'Origin: http://app-votes.com',
    'Referer: http://app-votes.com/',
    'Accept-Language: es-ES,es;q=0.9',
    // A veces la compresión gzip da problemas en cURL simple, la quitamos por seguridad
    // 'Accept-Encoding: gzip, deflate' 
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Importante: Seguir redirecciones y cookies
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, ""); // Habilita manejo de cookies en memoria

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Error cURL: ' . curl_error($ch);
} else {
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Respuesta del Servidor:\n" . $response;
}

?>