<div class="container mt-1">
    <a href="<?php echo htmlspecialchars($voltar_para); ?>" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <p class="text-muted">Por <?= htmlspecialchars($article['author']) ?> em <?= date('d/m/Y', strtotime($article['created_at'])) ?></p>

    <?php if ($article['image']): ?>
        <img src="/public<?= htmlspecialchars($article['image']) ?>" alt="Imagem do artigo" class="img-fluid mb-3">
    <?php endif; ?>

    <!-- Conteúdo do artigo com HTML básico permitido -->
    <div><?= nl2br(strip_tags($article['content'], '<p><br><strong><em><ul><ol><li><b><i>')) ?></div>

    <hr>

    <?php if (isset($_GET['denuncia'])): ?>
        <div class="alert alert-warning">
            A tua denúncia foi enviada com sucesso. Obrigado pela tua contribuição.
        </div>
    <?php elseif (isset($_GET['pending'])): ?>
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

                    <!-- Botão Denunciar -->
                    <br><button type="button"
                            class="btn btn-sm btn-outline-danger mt-2"
                            data-bs-toggle="modal"
                            data-bs-target="#reportModal<?= $c['id'] ?>">
                        Denunciar
                    </button>

            <!-- Modal de denúncia -->
            <div class="modal fade" id="reportModal<?= $c['id'] ?>" tabindex="-1" aria-labelledby="reportModalLabel<?= $c['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                <form method="POST" action="denunciar_comentario.php">
                    <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">

                    <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel<?= $c['id'] ?>">Denunciar Comentário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <div class="modal-body">
                    <p><strong>Comentário de:</strong> <?= htmlspecialchars($c['name']) ?></p>
                    <p><em>"<?= htmlspecialchars($c['comment']) ?>"</em></p>

                    <div class="mb-3">
                        <label for="reporter_name<?= $c['id'] ?>" class="form-label">O seu nome</label>
                        <input type="text" class="form-control" id="reporter_name<?= $c['id'] ?>" name="reporter_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="reporter_email<?= $c['id'] ?>" class="form-label">O seu email</label>
                        <input type="email" class="form-control" id="reporter_email<?= $c['id'] ?>" name="reporter_email" required>
                    </div>

                    <div class="mb-3">
                        <label for="reason<?= $c['id'] ?>" class="form-label">Motivo da denúncia</label>
                    <textarea class="form-control" name="reason" id="reason<?= $c['id'] ?>" rows="3" required style="resize: none;"></textarea>
                    </div>
                    </div>

                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-flag"></i> Enviar Denúncia
                    </button>
                    </div>

                </form>
                </div>
            </div>
            </div>
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
            <input type="text" id="name" name="name" 
                class="form-control <?= isset($_SESSION['user']) ? 'bg-light text-muted' : '' ?>" 
                value="<?= htmlspecialchars($name) ?>" 
                <?= isset($_SESSION['user']) ? 'readonly' : '' ?>>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" id="email" name="email" 
                class="form-control <?= isset($_SESSION['user']) ? 'bg-light text-muted' : '' ?>" 
                value="<?= htmlspecialchars($email) ?>" 
                <?= isset($_SESSION['user']) ? 'readonly' : '' ?>>
            <div class="form-text">O teu email não será publicado.</div>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Comentário (máx 100 caracteres) *</label>
            <textarea id="comment" name="comment" 
                      class="form-control" 
                      maxlength="100" rows="3" required 
                      style="resize: none;"><?= htmlspecialchars($comment) ?></textarea>
        </div>

        <button type="submit" name="submit_comment" class="btn btn-primary">Enviar Comentário</button>
    </form>
</div>