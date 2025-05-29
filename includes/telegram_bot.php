<?php

require_once __DIR__ . '/env_loader.php';
loadEnv(__DIR__ . '/../.env');

function notificarNovoUtilizador($nome, $email) {

    $token = getenv('BOT_TOKEN');
    $chat_id = getenv('CHAT_ID');

    $mensagem = "🚨 Novo utilizador registado!\n"
              . "👤 Nome: $nome\n"
              . "📧 Email: $email\n"
              . "📆 Data: " . date("d/m/Y H:i");

    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $mensagem
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}