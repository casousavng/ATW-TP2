<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/mailer.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Artigo inválido.");
}

$artigo_ja_guardado = false;

$articleId = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT a.*, u.name AS author 
    FROM articles a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.id = ? AND a.is_visible = 1
");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    die("Artigo não encontrado.");
}

$errors = [];
$comment = '';

// Preencher nome e email se o utilizador estiver autenticado
if (isset($_SESSION['user'])) {
    $name = $_SESSION['user']['name'];
    $email = $_SESSION['user']['email'];
} else {
    $name = '';
    $email = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    // Apenas aceitar dados do form se não estiver autenticado
    if (!isset($_SESSION['user'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
    }

    $comment = trim($_POST['comment'] ?? '');

    if (empty($name)) $errors[] = "O nome é obrigatório.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (empty($comment)) {
        $errors[] = "O comentário é obrigatório.";
    } elseif (mb_strlen($comment) > 100) {
        $errors[] = "O comentário não pode ter mais de 100 caracteres.";
    }

    if (empty($errors)) {
        // Verificar se precisa de verificação de email
        $isVerified = isset($_SESSION['user']) ? 1 : 0;
        $token = $isVerified ? null : bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("
            INSERT INTO comments (article_id, name, email, comment, token, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$articleId, $name, $email, $comment, $token, $isVerified]);

        // Enviar email só se não estiver autenticado
        if (!$isVerified) {
            sendCommentVerificationEmail($email, $name, $token);
            header("Location: artigo.php?id=$articleId&pending=1");
            exit;
        } else {
            header("Location: artigo.php?id=$articleId");
            exit;
        }
    }
}

$stmt = $pdo->prepare("
    SELECT * FROM comments 
    WHERE article_id = ? AND is_verified = 1 
    ORDER BY created_at DESC
");
$stmt->execute([$articleId]);
$comments = $stmt->fetchAll();

$voltar_para = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'artigos.php';

// Tratamento de artigos guardados

if (!$article) {
    echo "Artigo não encontrado.";
    exit;
}

// Verifica se o utilizador está autenticado
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

// Guardar o conteúdo se for pedido
if ($isLoggedIn && isset($_POST['guardar_artigo'])) {
    if (!$artigo_ja_guardado) {
        $stmt = $pdo->prepare("
            INSERT INTO conteudos_guardados (user_id, conteudo_id, tipo_conteudo)
            VALUES (:user_id, :conteudo_id, 'artigo')
        ");
        $stmt->execute([
            'user_id' => $user_id,
            'conteudo_id' => $articleId
        ]);
        $guardado_sucesso = true;
        $artigo_ja_guardado = true;
    } else {
        $ja_guardado = true;
    }
}

$artigo_ja_guardado = false;

if ($isLoggedIn) {
    $check = $pdo->prepare("
        SELECT 1 FROM conteudos_guardados 
        WHERE user_id = :user_id AND conteudo_id = :conteudo_id AND tipo_conteudo = 'artigo'
    ");
    $check->execute([
        'user_id' => $user_id,
        'conteudo_id' => $articleId
    ]);
    $artigo_ja_guardado = $check->fetchColumn() ? true : false;
}

$user_id = $_SESSION['user_id'] ?? null;
$artigo_id = intval($_GET['id'] ?? 0);

if ($user_id && $artigo_id) {
    $sql = "UPDATE conteudos_guardados 
            SET vezes_consultado = vezes_consultado + 1 
            WHERE user_id = :uid 
              AND tipo_conteudo = 'artigo' 
              AND conteudo_id = :cid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'uid' => $user_id,
        'cid' => $artigo_id
    ]);
}
?>

<?php
include '../includes/header.php';
include '../views/public/artigo.php';
include '../includes/footer.php';
?>