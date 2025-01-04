<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

$header = [
    'alg' => 'ES256',
    'kid' => '5DC46LK3RB',
    'typ' => 'JWT'
];

$payload = [
    'iss' => 'AS5BHHK5AW',
    'iat' => time(),
    'exp' => time() + (60 * 60),
    'origin' => '*.polan.sk'
];

$encodedHeader = base64_encode(json_encode($header));
$encodedPayload = base64_encode(json_encode($payload));
$data = $encodedHeader . '.' . $encodedPayload;

$privateKeyPath = '/data/certs/AppleMaps/key.p8';
$privateKey = file_get_contents($privateKeyPath);

openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
$encodedSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

$jwtToken = $data . '.' . $encodedSignature;
echo $jwtToken;

?>
