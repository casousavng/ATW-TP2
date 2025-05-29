<?php
require_once("db.php");
include('../includes/header.php');

$token = $_GET['token'] ?? '';
$title = '';
$message = '';
$link = '';

if (!$token) {
    $title = "Token inválido";
    $message = "Nenhum token foi fornecido.";
} else {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE token = ? AND is_verified = 0");
    $stmt->execute([$token]);
    $comment = $stmt->fetch();

    if (!$comment) {
        $title = "Comentário já verificado ou token inválido";
        $message = "Este comentário já foi confirmado ou o link está incorreto.";
    } else {
        $stmt = $pdo->prepare("UPDATE comments SET is_verified = 1 WHERE id = ?");
        $stmt->execute([$comment['id']]);

        $title = "Comentário confirmado com sucesso!";
        $message = "Obrigado, <strong>" . htmlspecialchars($comment['name']) . "</strong>! O teu comentário agora está visível no artigo.";
        $link = "/public/artigo.php?id=" . $comment['article_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Comentário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow rounded-4">
                    <div class="card-body text-center p-5">
                        <h2 class="card-title mb-4"><?= $title ?></h2>
                        <p class="card-text fs-5"><?= $message ?></p>

                        <?php if ($link): ?>
                            <a href="<?= $link ?>" class="btn btn-primary mt-4">Voltar ao artigo</a>
                        <?php else: ?>
                            <a href="/public/artigos.php" class="btn btn-secondary mt-4">Ver artigos</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php include('../includes/footer.php'); ?>