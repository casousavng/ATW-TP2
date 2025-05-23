<?php

# This file is part of the InfinityFree project.

$host = "sql306.infinityfree.com";
$port = 3306;
$dbname = "if0_39034605_defaultdb";
$username = "if0_39034605";
$password = "yjEcdBUtoN";

// Montar a DSN
$conn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($conn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    exit;
}