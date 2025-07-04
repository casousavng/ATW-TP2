<div class="container mt-1">
    <form action="artigos.php" method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Pesquisar artigos..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-info" type="submit">
                <i class="bi bi-search"></i>
            </button>
            <a href="artigos.php" class="btn btn-secondary" title="Limpar">
                <i class="bi bi-dash-circle"></i>
            </a>
        </div>
    </form>

    <?php if (count($artigos) === 0): ?>
        <p class="text-muted">Nenhum artigo encontrado.</p>
    <?php else: ?>
        <div class="masonry">
            <?php foreach ($artigos as $artigo): ?>
                <div class="masonry-item mb-4">
                    <a href="artigo.php?id=<?= (int)$artigo['id'] ?>" class="text-decoration-none text-reset">
                        <div class="card">
                            <?php if (!empty($artigo['image'])): ?>
                                <img src="/public<?= htmlspecialchars($artigo['image']) ?>" class="card-img-top" alt="Imagem do artigo">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($artigo['title']) ?></h5>
                               <p class="card-text"><?= getExcerptWithMore($artigo['content'], $artigo['id']) ?></p
                                <p class="text-muted mb-1">
                                    Por <?= htmlspecialchars($artigo['author']) ?> em <?= date('d/m/Y', strtotime($artigo['created_at'])) ?>
                                </p>
                                <p class="text-muted">
                                    <?= (int)$artigo['comments_count'] ?> comentário<?= ((int)$artigo['comments_count'] === 1 ? '' : 's') ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<button id="backToTopBtn" title="Voltar ao topo">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../assets/script/script.js"></script>
