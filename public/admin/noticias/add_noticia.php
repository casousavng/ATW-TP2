<?php
define('BASE_PATH', dirname(__DIR__, 3));  // Caminho base ajustado para corrigir os erros
require_once BASE_PATH . '/includes/db.php';  // Caminho correto para db.php
require_once BASE_PATH . '/includes/auth.php';  // Caminho correto para auth.php
checkAdmin();  // Verifica se o usuário é administrador

// Variáveis para o sucesso ou erro no upload
$sucesso = false;
$erro = '';

// Processar o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    
    // Processar imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem'];
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $nomeFicheiro = 'noticia_' . time() . '.' . $extensao;
        $destino = BASE_PATH . '/public/uploads/noticias/' . $nomeFicheiro;

        // Mover imagem para o diretório correto
        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            // Inserir dados no banco de dados
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, imagem, texto, data_criacao) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$titulo, $nomeFicheiro, $texto]);
            $sucesso = true;
        } else {
            $erro = "Erro ao carregar a imagem.";
        }
    } else {
        $erro = "Erro ao carregar a imagem.";
    }
}

// Buscar todas as notícias para exibir
$stmt = $pdo->query("SELECT * FROM noticias ORDER BY data_criacao DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Utilizadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>

    <h2 class="mt-1">Todas as Notícias</h2>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data de Inserção</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($noticias)): ?>
                <tr>
                    <td colspan="3" class="text-center">Não há notícias publicadas de momento.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($noticias as $noticia): ?>
                    <tr>
                        <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($noticia['data_criacao'])) ?></td>
                        <td>
                            <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="apagar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-danger btn-sm">Apagar</a>
                            <a href="ocultar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm <?= $noticia['visivel'] ? 'btn-secondary' : 'btn-success' ?>">
                            <?= $noticia['visivel'] ? 'Ocultar' : 'Mostrar' ?>
</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <hr>
    <h2>Adicionar Nova Notícia</h2>
    
    <?php if ($sucesso): ?>
        <div class="alert alert-success">Notícia adicionada com sucesso!</div>
    <?php elseif (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem:</label>
            <input type="file" name="imagem" id="imagem" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="texto" class="form-label">Texto:</label>
            <textarea name="texto" id="texto" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Adicionar Notícia</button>
        <button type="reset" class="btn btn-secondary">Limpar Campos</button>
    </form>
</main>

<?php include BASE_PATH . '/includes/footer.php'; ?> <!-- Caminho correto para footer.php -->