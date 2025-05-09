<?php

$uri = "mysql://avnadmin:AVNS_oByomHGLFi4pGWNcKxO@projeto-atw-ispgaya-bf27.j.aivencloud.com:11682/defaultdb?ssl-mode=REQUIRED";

$fields = parse_url($uri);

$conn = "mysql:";
$conn .= "host=" . $fields["host"];
$conn .= ";port=" . $fields["port"];
$conn .= ";dbname=defaultdb";
$conn .= ";sslmode=verify-ca;sslrootcert=ca.pem";

try {
  $pdo = new PDO($conn, $fields["user"], $fields["pass"]);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // Linha de debug removida ou comentada:
  // $stmt = $pdo->query("SELECT VERSION()");
  // echo $stmt->fetch()[0];
} catch (Exception $e) {
  echo "Erro: " . $e->getMessage();
  exit;
}