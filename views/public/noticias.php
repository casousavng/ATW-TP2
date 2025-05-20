<?php
// views/public/noticias.php (VIEW)

// Não há lógica de consulta ao DB ou processamento de requisição aqui.
// Apenas exibe o HTML e as variáveis que já foram preparadas pelo controller ($searchQuery, $noticias).
?>

<main class="container mt-1">
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
                <?php if ($noticia['imagem']): ?>
                    <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="img-fluid">
                <?php endif; ?>
                <p><small>Publicado em: <?= date('d/m/Y H:i', strtotime($noticia['data_criacao'])) ?></small></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>