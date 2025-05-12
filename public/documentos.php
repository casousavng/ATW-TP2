<?php
require_once("../includes/db.php"); // Liga à base de dados

// Verificar se há um parâmetro de pesquisa
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Ajustar a consulta SQL para filtrar os documentos com base na pesquisa
if ($searchQuery) {
    $stmt = $pdo->prepare("
        SELECT nome_personalizado, nome_ficheiro 
        FROM documentos 
        WHERE nome_personalizado LIKE ? OR nome_ficheiro LIKE ? 
        ORDER BY data_upload DESC
    ");
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    // Caso não haja pesquisa, exibe todos os documentos
    $stmt = $pdo->query("SELECT nome_personalizado, nome_ficheiro FROM documentos ORDER BY data_upload DESC");
}

$documentos = $stmt->fetchAll();
?>

<?php include("../includes/header.php"); ?>

<main class="container mt-1">
    <!-- Formulário de pesquisa -->
    <form action="documentos.php" method="get" class="mb-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar documentos..." value="<?= htmlspecialchars($searchQuery) ?>">
    </form>

    <h2>Lista de Documentos</h2>
    <ul class="list-group">
        <?php if (count($documentos) > 0): ?>
            <?php foreach ($documentos as $doc): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($doc['nome_personalizado']) ?></span>
                    <a href="/uploads/documentos/<?= urlencode($doc['nome_ficheiro']) ?>" download class="btn btn-outline-primary btn-sm">
                        Descarregar
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item">Nenhum documento disponível.</li>
        <?php endif; ?>
    </ul>
</main>

<?php include("../includes/footer.php"); ?>