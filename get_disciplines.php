<?php
require_once(__DIR__ . '/../../config.php');
require_login();

$cursoid = optional_param('idCursoInstituicao', 0, PARAM_INT);
$token = get_config('mod_intersaberesplay', 'token');

header('Content-Type: application/json');

if (empty($token)) {
    echo json_encode([]);
    exit;
}

$urbBase   = 'https://play.intersaberes.com';
if (defined('INTERSABERES_PLAY_URL')) {
    $urbBase   = INTERSABERES_PLAY_URL;
}

$apiurl = "{$urbBase}/api/instituicao/listar-disciplinas?token={$token}" . ($cursoid ? "&idCursoInstituicao={$cursoid}" : "");

// usando cURL para maior compatibilidade
$ch = curl_init($apiurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!is_array($data)) $data = [];

echo json_encode($data['lista'] ?? []);
