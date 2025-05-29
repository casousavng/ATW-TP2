<?php
// views/public/noticia.php (VIEW)
?>

<main class="container mt-1">
    <a href="<?php echo htmlspecialchars($voltar_para); ?>" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Voltar</a>

    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
    <p><small class="text-muted">Publicado em: <?= date('d/m/Y H:i', strtotime($noticia['data_criacao'])) ?></small></p>

    <?php if ($noticia['imagem']): ?>
        <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="img-fluid mb-3">
    <?php endif; ?>

    <article>
        <?= nl2br(htmlspecialchars($noticia['texto'])) ?>
    </article>
</main>