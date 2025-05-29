<?php
// Conexão com a base de dados InfinityFree

$env = parse_ini_file(__DIR__ . '/../.env');

try {
    $host     = $env['IF_DB_HOST'];
    $port     = $env['IF_DB_PORT'];
    $dbname   = $env['IF_DB_NAME'];
    $user     = $env['IF_DB_USER'];
    $password = $env['IF_DB_PASSWORD'];

    $connStr = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    $pdo = new PDO($connStr, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    echo "Erro de ligação à base de dados InfinityFree: " . $e->getMessage();
    exit;
}