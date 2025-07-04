<main class="container mt-1">
    <form action="documentos.php" method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Pesquisar documentos..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-info" type="submit">
                <i class="bi bi-search"></i>
            </button>
            <a href="documentos.php" class="btn btn-secondary" title="Limpar">
                <i class="bi bi-dash-circle"></i>
            </a>
        </div>
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

<button id="backToTopBtn" title="Voltar ao topo">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../assets/script/script.js"></script>