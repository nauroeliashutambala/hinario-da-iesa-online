<?php

// Endpoint de chat - exemplo básico<?php

// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input["mensagens"]) || !is_array($input["mensagens"])) {
    http_response_code(400);
    echo json_encode(["erro" => "Faltam mensagens ou formato inválido"]);
    exit;
}

$mensagens = $input["mensagens"];
if (count($mensagens) === 0) {
    http_response_code(400);
    echo json_encode(["erro" => "Nenhuma mensagem enviada"]);
    exit;
}

$prompt = "";
foreach ($mensagens as $m) {
    if (!isset($m["role"], $m["content"])) {
        http_response_code(400);
        echo json_encode(["erro" => "Mensagem mal formatada"]);
        exit;
    }
    $role = ucfirst(substr($m["role"], 0, 20));
    $content = substr($m["content"], 0, 500);
    $prompt .= $role . ": " . $content . "\n";
}

$prompt .= "\nResponde de forma cristã, com amor e humildade, como uma IA da IESA. Se possível, cita versículos que reforcem a mensagem.";

$body = [
    "n" => 1,
    "prompt" => $prompt,
    "temperature" => 0.4,
    "top_p" => 0.9
];

$ch = curl_init("https://us-central1-conquer-apps-2ad61.cloudfunctions.net/prod/api.live");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(["erro" => "Falha ao conectar com a API", "detalhe" => $curl_error]);
    exit;
}

$data = json_decode($response, true);
if (!is_array($data) || !isset($data["choices"][0]["message"]["content"])) {
    http_response_code(500);
    echo json_encode(["erro" => "Resposta inesperada da IA"]);
    exit;
}

$resposta = $data["choices"][0]["message"]["content"];
echo json_encode(["resposta" => $resposta]);
?>