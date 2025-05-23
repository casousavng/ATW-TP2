<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Imagens em destaque
$stmt = $pdo->query("SELECT caminho FROM imagem_destaque ORDER BY atualizado_em DESC");
$imagensDestaque = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Últimas 3 notícias
$stmtNoticias = $pdo->query("SELECT titulo, imagem, texto FROM noticias WHERE visivel = 1 ORDER BY data_criacao DESC LIMIT 3");
$noticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

// Últimos 3 artigos
$stmtArtigos = $pdo->query("SELECT title, image, content FROM articles WHERE is_visible = 1 ORDER BY created_at DESC LIMIT 3");
$artigos = $stmtArtigos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Comunidade Desportiva - Artigos, Notícias e partilha de Documentos">
    <meta name="keywords" content="Comunidade Desportiva, Artigos, Notícias, Documentos">
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/styles_index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Comunidade Desportiva</title>

    <style>
        .carousel-slide {
            animation: fadeSlide <?= count($imagensDestaque) * 5 ?>s infinite;
        }

        .content-text {
            -webkit-line-clamp: 6;
        }

        @media (max-width: 768px) {
            .content-text {
                -webkit-line-clamp: 5;
            }
        }
    </style>
</head>

<body class="bg-light">

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/public/service-worker.js')
      .then(reg => console.log('SW registrado!', reg))
      .catch(err => console.error('SW falhou', err));
  }
</script>

<div class="container py-2">

    <!-- Quem Somos -->
    <section class="mb-4">
        <h2>Quem Somos</h2>
        <p>Somos uma comunidade dedicada à prática e promoção do desporto em todas as idades.</p>
    </section>

    <!-- Imagens em Destaque -->
    <section class="mb-4">
        <h2>Imagens em Destaque</h2>

        <?php if (!empty($imagensDestaque)): ?>
            <div class="carousel-container">
                <?php foreach ($imagensDestaque as $index => $img): ?>
                    <div class="carousel-slide" style="animation-delay: <?= $index * 5 ?>s">
                        <img src="/public/uploads/destaque/<?= htmlspecialchars($img) ?>" alt="Imagem destaque">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Ainda não há imagens em destaque.</p>
        <?php endif; ?>
    </section>

    <!-- Últimas Notícias -->
    <section class="mb-4">
        <h2>Últimas Notícias</h2>

        <?php if (!empty($noticias)): ?>
            <div class="scroll-wrapper">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="content-card">
                        <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem da notícia">
                        <h5><?= htmlspecialchars($noticia['titulo']) ?></h5>
                        <p class="content-text"><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Ainda não há notícias publicadas.</p>
        <?php endif; ?>
    </section>

    <!-- Últimos Artigos -->
    <section class="mb-4">
        <h2>Últimos Artigos</h2>

        <?php if (!empty($artigos)): ?>
            <div class="scroll-wrapper">
                <?php foreach ($artigos as $artigo): ?>
                    <div class="content-card">
                        <?php if ($artigo['image']): ?>
                            <img src="/public<?= htmlspecialchars($artigo['image']) ?>" alt="Imagem do artigo">
                        <?php endif; ?>
                        <h5><?= htmlspecialchars($artigo['title']) ?></h5>
                        <p class="content-text"><?= nl2br(htmlspecialchars($artigo['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Ainda não há artigos publicados.</p>
        <?php endif; ?>
    </section>

</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>