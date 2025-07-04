<main class="container mt-1">
    <a href="<?php echo htmlspecialchars($voltar_para); ?>" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Voltar</a>
    <h1 class="mb-3"><?= htmlspecialchars($noticia['titulo']) ?></h1>
    
    <!-- Botão Guardar Notícia -->
    <?php if (isset($_SESSION['user'])): ?>
        <form method="POST" class="mt-4">
            <?php if (isset($guardada_sucesso)): ?>
                <div class="alert alert-success">Notícia guardada com sucesso!</div>
            <?php elseif (isset($ja_guardada)): ?>
                <div class="alert alert-warning">Esta notícia já está guardada.</div>
            <?php endif; ?>
                <button 
                    type="submit" 
                    name="guardar_noticia" 
                    class="btn <?= $noticia_ja_guardada ? 'btn-primary disabled' : 'btn-outline-primary' ?>" 
                    <?= $noticia_ja_guardada ? 'disabled' : '' ?>
                >
                    <?php if ($noticia_ja_guardada): ?>
                        <i class="bi bi-bookmark-check-fill"></i> Notícia Guardada
                    <?php else: ?>
                        <i class="bi bi-bookmark-plus"></i> Guardar Notícia
                    <?php endif; ?>
                </button>
        </form>
    <?php else: ?>
        <p class="text-muted mt-3">Inicia sessão para poderes guardar esta notícia.</p>
    <?php endif; ?>

    <br>

    <p><small class="text-muted">Publicado em: <?= date('d/m/Y H:i', strtotime($noticia['data_criacao'])) ?></small></p>

    <?php if ($noticia['imagem']): ?>
        <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="img-fluid mb-3">
    <?php endif; ?>

    <article>
        <?= nl2br(htmlspecialchars($noticia['texto'])) ?>
    </article>
</main>