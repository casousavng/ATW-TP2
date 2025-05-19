<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Obter as imagens em destaque (ordenadas pelo mais recente)
$stmt = $pdo->query("SELECT caminho FROM imagem_destaque ORDER BY atualizado_em DESC");
$imagensDestaque = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Quem Somos -->
<section class="mb-4">
    <h2>Quem Somos</h2>
    <p>Somos uma comunidade dedicada à prática e promoção do desporto em todas as idades.</p>
</section>

<!-- Imagens em Destaque (Slideshow CSS) -->
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

        <style>
            .carousel-container {
                position: relative;
                width: 100%;
                aspect-ratio: 16 / 9; /* Mantém proporção visual padrão */
                overflow: hidden;
                background-color: #000; /* Evita flashes brancos se imagem demorar */
            }

            .carousel-slide {
                position: absolute;
                width: 100%;
                height: 100%;
                opacity: 0;
                animation: fadeSlide <?= count($imagensDestaque) * 5 ?>s infinite;
            }

            .carousel-slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;a
            }

            @keyframes fadeSlide {
                0% { opacity: 0; }
                5% { opacity: 1; }
                25% { opacity: 1; }
                30% { opacity: 0; }
                100% { opacity: 0; }
            }
        </style>

    <?php else: ?>
        <p class="text-muted">Ainda não há imagens em destaque.</p>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>