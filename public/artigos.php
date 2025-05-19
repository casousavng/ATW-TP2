<?php
require_once '../includes/db.php';

// Verificar se há um parâmetro de pesquisa
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Ajustar a consulta SQL para filtrar os artigos com base na pesquisa
if ($searchQuery) {
    // A consulta abaixo vai procurar o termo de pesquisa em qualquer parte do título ou conteúdo
    $stmt = $pdo->prepare("
        SELECT a.id, a.title, a.image, a.content, a.created_at, u.name AS author
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.is_visible = 1 
        AND (a.title LIKE ? OR a.content LIKE ?)
        ORDER BY a.created_at DESC
    ");
    // O '%' antes e depois da pesquisa é necessário para procurar em qualquer parte do texto
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    // Caso não haja pesquisa, exibe todos os artigos
    $stmt = $pdo->prepare("
        SELECT a.id, a.title, a.image, a.content, a.created_at, u.name AS author
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.is_visible = 1
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
}

$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('../includes/header.php'); ?>

<!-- Conteúdo -->
<div class="container mt-1">
    <!-- Formulário de pesquisa -->
    <form action="artigos.php" method="get" class="mb-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar artigos..." value="<?= htmlspecialchars($searchQuery) ?>">
    </form>

    <?php if (count($artigos) === 0): ?>
        <p class="text-muted">Nenhum artigo encontrado.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($artigos as $artigo): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if ($artigo['image']): ?>
                            <img src="../public/<?= htmlspecialchars($artigo['image']) ?>" class="card-img-top" alt="Imagem do artigo">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($artigo['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(mb_substr(strip_tags($artigo['content']), 0, 200)) ?>...</p>
                            <p class="text-muted">Por <?= htmlspecialchars($artigo['author']) ?> em <?= date('d/m/Y', strtotime($artigo['created_at'])) ?></p>
                            <!-- Botão para ver artigo completo futuramente -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>