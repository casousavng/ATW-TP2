<?php
// Conexão com a base de dados AIVEN

$env = parse_ini_file(__DIR__ . '/../.env');

try {
    $user     = $env['DB_USER'];
    $password = $env['DB_PASSWORD'];
    $host     = $env['DB_HOST'];
    $port     = $env['DB_PORT'];
    $dbname   = $env['DB_NAME'];
    $ssl_mode = $env['DB_SSL_MODE'];

    $connStr = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    $options = [
        PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../ca.pem',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    $pdo = new PDO($connStr, $user, $password, $options);

} catch (Exception $e) {
    echo "Erro de ligação à base de dados AIVEN: " . $e->getMessage();
    exit;
}