<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT cg.id AS guardado_id, cg.tipo_conteudo, cg.data_guardado, cg.vezes_consultado,
           COALESCE(a.title, n.titulo) AS titulo,
           COALESCE(a.id, n.id) AS conteudo_id
    FROM conteudos_guardados cg
    LEFT JOIN articles a ON (cg.tipo_conteudo = 'artigo' AND cg.conteudo_id = a.id)
    LEFT JOIN noticias n ON (cg.tipo_conteudo = 'noticia' AND cg.conteudo_id = n.id)
    WHERE cg.user_id = :uid
    ORDER BY cg.data_guardado DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $user_id]);
$conteudos_guardados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Para remover favorito - opcional
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_id'])) {
    $remover_id = intval($_POST['remover_id']);
    $stmtDel = $pdo->prepare("DELETE FROM conteudos_guardados WHERE id = :id AND user_id = :uid");
    $stmtDel->execute(['id' => $remover_id, 'uid' => $user_id]);
    header('Location: guardados.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Meus Conteúdos Guardados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/styles.css" />
</head>

<body>
<div class="container mt-4">
    <a href="index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <h1 class="mb-4">Meus Conteúdos Guardados</h1>

    <?php if (empty($conteudos_guardados)): ?>
        <p class="text-muted">Ainda não guardaste nenhum artigo ou notícia.</p>
    <?php else: ?>
        <!-- Tabela -->
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Data Guardado</th>
                    <th>Consultado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conteudos_guardados as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['titulo']) ?></td>
                        <td><?= ucfirst($c['tipo_conteudo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($c['data_guardado'])) ?></td>
                        <td><?= intval($c['vezes_consultado']) ?></td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Ações do conteúdo">
                                <?php
                                $url = $c['tipo_conteudo'] === 'artigo' 
                                    ? "/public/artigo.php?id={$c['conteudo_id']}" 
                                    : "/public/noticia.php?id={$c['conteudo_id']}";
                                ?>
                                <a href="<?= $url ?>" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="bi bi-eye"></i> <span class="d-none d-sm-inline">Ver</span>
                                </a>

                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $c['guardado_id'] ?>">
                                    <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Apagar</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form method="POST" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        Tem certeza que deseja apagar este item? <br><strong>Esta ação não poderá ser desfeita.</strong>
                        <input type="hidden" name="remover_id" id="delete-article-id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Apagar</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <button id="backToTopBtn" title="Voltar ao topo">
        <i class="bi bi-arrow-up"></i>
    </button>


</div>

<!-- Bootstrap JS Bundle para modal funcionar -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script src="../../assets/script/script.js"></script>


<script>
// Preenche o ID do conteúdo guardado no modal ao abrir
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const guardadoId = button.getAttribute('data-id');
    document.getElementById('delete-article-id').value = guardadoId;
});
</script>

</body>
</html>