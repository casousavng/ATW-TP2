<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();

    if ($doc) {
        $filePath = BASE_PATH . '/public/uploads/documentos/' . $doc['nome_ficheiro'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $del = $pdo->prepare("DELETE FROM documentos WHERE id = ?");
        $del->execute([$id]);
    }
}

header('Location: manage_documents.php');
exit;