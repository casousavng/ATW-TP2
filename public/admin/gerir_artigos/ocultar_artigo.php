<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

if (!isset($_GET['id'], $_GET['action'])) {
    header('Location: artigos.php');
    exit;
}

$id = (int) $_GET['id'];
$action = $_GET['action'] === 'hide' ? 0 : 1;

$stmt = $pdo->prepare("UPDATE articles SET is_visible = :visible WHERE id = :id");
$stmt->execute([
    ':visible' => $action,
    ':id' => $id
]);

header('Location: artigos.php');
exit;