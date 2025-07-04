<main class="container mt-1">
    <form action="noticias.php" method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Pesquisar notícias..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-info" type="submit">
                <i class="bi bi-search"></i>
            </button>
            <a href="noticias.php" class="btn btn-secondary" title="Limpar">
                <i class="bi bi-dash-circle"></i>
            </a>
        </div>
    </form>

    <?php if (empty($noticias)): ?>
        <div class="alert alert-warning" role="alert">
            Não há notícias disponíveis no momento.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($noticias as $noticia): ?>
                <div class="col-md-6 mb-4">
                    <a href="noticia.php?id=<?= $noticia['id'] ?>" class="text-decoration-none text-reset">
                        <article class="card h-100">
                            <?php if ($noticia['imagem']): ?>
                                <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="card-img-top">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                                <p class="card-text"><?= getExcerptWithMore($noticia['texto'], $noticia['id']) ?></p>
                                <span><small class="text-muted">Publicado em: <?= date('d/m/Y H:i', strtotime($noticia['data_criacao'])) ?></small></span>
                            </div>
                        </article>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<button id="backToTopBtn" title="Voltar ao topo">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../assets/script/script.js"></script>