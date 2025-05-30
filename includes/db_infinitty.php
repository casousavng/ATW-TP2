<?php
// Conexão com a base de dados InfinityFree

try {

    // conta andresneakersousa
    //$host     = 'sql106.infinityfree.com';
    //$port     = '3306';
    //$dbname   = 'if0_39105337_atw';
    //$user     = 'if0_39105337';
    //$password = 'IAMYwy0pmxPks';

    // conta ispg2022105675
    $host     = 'sql211.infinityfree.com';
    $port     = '3306';
    $dbname   = 'if0_39035297_defaultdb';
    $user     = 'if0_39035297';
    $password = 'pNdRGSmqY5S';

    $connStr = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    $pdo = new PDO($connStr, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    echo "Erro de ligação à base de dados InfinityFree: " . $e->getMessage();
    exit;
}