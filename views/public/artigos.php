<?php
// views/public/artigos.php (VIEW)

// Não há lógica de consulta ao DB ou processamento de requisição aqui.
// Apenas exibe o HTML e as variáveis que já foram preparadas pelo controller ($artigos, $searchQuery).
?>

<div class="container mt-1">
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
                            <img src="/public<?= htmlspecialchars($artigo['image']) ?>" class="card-img-top" alt="Imagem do artigo">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($artigo['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(mb_substr(strip_tags($artigo['content']), 0, 200)) ?>...</p>
                            <p class="text-muted">Por <?= htmlspecialchars($artigo['author']) ?> em <?= date('d/m/Y', strtotime($artigo['created_at'])) ?></p>
                            </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>