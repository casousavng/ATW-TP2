<div class="container mt-1">
    <a href="<?php echo htmlspecialchars($voltar_para); ?>" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Voltar</a>

    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <p class="text-muted">Por <?= htmlspecialchars($article['author']) ?> em <?= date('d/m/Y', strtotime($article['created_at'])) ?></p>

    <?php if ($article['image']): ?>
        <img src="/public<?= htmlspecialchars($article['image']) ?>" alt="Imagem do artigo" class="img-fluid mb-3">
    <?php endif; ?>

    <div><?= nl2br(htmlspecialchars($article['content'])) ?></div>

    <hr>

    <?php if (isset($_GET['pending'])): ?>
        <div class="alert alert-info">
            Comentário enviado! Por favor verifica o teu email para confirmar.
        </div>
    <?php endif; ?>

    <h3>Comentários (<?= count($comments) ?>)</h3>

    <?php if ($comments): ?>
        <ul class="list-group mb-4">
            <?php foreach ($comments as $c): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($c['name']) ?></strong> disse:<br>
                    <?= htmlspecialchars($c['comment']) ?><br>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">Nenhum comentário confirmado ainda.</p>
    <?php endif; ?>

    <h4>Adicionar comentário</h4>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="mb-5" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nome *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            <div class="form-text">O teu email não será publicado.</div>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Comentário (máx 100 caracteres) *</label>
            <textarea id="comment" name="comment" class="form-control" maxlength="100" rows="3" required style="resize: none;"><?= htmlspecialchars($comment) ?></textarea>
        </div>

        <button type="submit" name="submit_comment" class="btn btn-primary">Enviar Comentário</button>
    </form>
</div>