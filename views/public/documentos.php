<?php
// views/public/documentos.php (VIEW)
// Não há lógica de consulta ao DB ou processamento de requisição aqui.
// Apenas exibe o HTML e as variáveis que já foram preparadas pelo controller ($searchQuery, $documentos).
?>

<main class="container mt-1">
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