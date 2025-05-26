<?php
function notificarNovoUtilizador($nome, $email) {
    
    $token = '7636402519:AAEOSCZSl_F1SZAEe7rZfsJF8diiZgRO9p8'; // ← Token do teu bot
    $chat_id = '5390134101'; // ← CHAT_ID do teu bot

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