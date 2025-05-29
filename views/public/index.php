<?php
// views/public/index.php (VIEW)
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Comunidade Desportiva - Artigos, Notícias e partilha de Documentos" />
    <meta name="keywords" content="Comunidade Desportiva, Artigos, Notícias, Documentos" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/css/style_index.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <title>Comunidade Desportiva</title>

    <style>

        .carousel-slide {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            /* Usaremos animação para alternar */
            animation: fadeSlide <?= count($imagensDestaque) * 5 ?>s infinite;
        }
        <?php foreach ($imagensDestaque as $index => $img): ?>
            .carousel-slide:nth-child(<?= $index + 1 ?>) {
                animation-delay: <?= $index * 5 ?>s;
            }
        <?php endforeach; ?>

        /* RESPONSIVO */
        @media (max-width: 992px) {
            .content-text {
                -webkit-line-clamp: 5;
                max-height: calc(1.3em * 5);
            }
        }

        @media (max-width: 576px) {
            .content-text {
            -webkit-line-clamp: 4;
            max-height: calc(1.3em * 4);
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
        <p><strong>Vivemos e respiramos desporto em todas as suas formas.</strong>
            Trazemos-te notícias atuais, artigos profundos e partilhas autênticas sobre todas as modalidades, dos grandes palcos aos recantos menos conhecidos.
            Aqui, celebramos a paixão dos verdadeiros amantes do desporto, com informação, opinião e emoção.
            Se o desporto faz parte da tua vida, este é o teu lugar.</p>
    </section>

    <!-- Imagens em Destaque -->
    <section class="mb-4">
        <h2>Imagens em Destaque</h2><br>
    
        <?php if (!empty($imagensDestaque)): ?>
            <div class="carousel-container">
                <?php foreach ($imagensDestaque as $img): ?>
                    <div class="carousel-slide">
                        <img src="/public/uploads/destaque/<?= htmlspecialchars($img) ?>" alt="Imagem destaque" loading="lazy" />
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Ainda não há imagens em destaque.</p>
        <?php endif; ?>
    </section>

    <!-- Últimas Notícias -->
    <section class="mb-4">
        <h2>Últimas Notícias</h2><br>

        <?php if (!empty($noticias)): ?>
            <div class="cards-container">
                <?php foreach ($noticias as $noticia): ?>
                    <a href="noticia.php?id=<?= urlencode($noticia['id']) ?>" class="content-card" tabindex="0">
                        <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem da notícia" loading="lazy" />
                        <div class="content-card-content">
                            <h5><?= htmlspecialchars($noticia['titulo']) ?></h5>
                            <p class="content-text"><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Ainda não há notícias publicadas.</p>
        <?php endif; ?>
    </section>

    <!-- Últimos Artigos -->
    <section class="mb-4">
        <h2>Últimos Artigos</h2><br>

        <?php if (!empty($artigos)): ?>
            <div class="cards-container">
                <?php foreach ($artigos as $artigo): ?>
                    <a href="artigo.php?id=<?= urlencode($artigo['id']) ?>" class="content-card" tabindex="0">
                        <?php if ($artigo['image']): ?>
                            <img src="/public<?= htmlspecialchars($artigo['image']) ?>" alt="Imagem do artigo" loading="lazy" />
                        <?php endif; ?>
                        <div class="content-card-content">
                            <h5><?= htmlspecialchars($artigo['title']) ?></h5>
                            <p class="content-text"><?= nl2br(htmlspecialchars($artigo['content'])) ?></p>
                            <div class="comentarios-count"><?= $artigo['comentarios_count'] ?> comentário<?= ($artigo['comentarios_count'] != 1) ? 's' : '' ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Ainda não há artigos publicados.</p>
        <?php endif; ?>
    </section>

</div>
</body>
</html>