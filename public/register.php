<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Verificar se há um parâmetro de pesquisa
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Ajustar a consulta SQL para filtrar as notícias com base na pesquisa
if ($searchQuery) {
    $stmt = $pdo->prepare("
        SELECT id, titulo, imagem, texto 
        FROM noticias 
        WHERE visivel = 1 
        AND (titulo LIKE ? OR texto LIKE ?)
        ORDER BY data_criacao DESC
    ");
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    // Caso não haja pesquisa, exibe todas as notícias
    $stmt = $pdo->query("SELECT id, titulo, imagem, texto FROM noticias WHERE visivel = 1 ORDER BY data_criacao DESC");
}

$noticias = $stmt->fetchAll();
?>

<main class="container mt-1">
    <!-- Formulário de pesquisa -->
    <form action="noticias.php" method="get" class="mb-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar notícias..." value="<?= htmlspecialchars($searchQuery) ?>">
    </form>

    <?php if (empty($noticias)): ?>
        <div class="alert alert-warning" role="alert">
            Não há notícias disponíveis no momento.
        </div>
    <?php else: ?>
        <?php foreach ($noticias as $noticia): ?>
            <article class="mb-4">
                <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
                <p><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>
                <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="img-fluid">
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>