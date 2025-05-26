<?php
require_once __DIR__ . '/../includes/db.php';

$nomes = [
    ['João', 'Silva'],
    ['Maria', 'Oliveira'],
    ['Carlos', 'Pereira'],
    ['Ana', 'Santos'],
    ['Miguel', 'Costa'],
    ['Rita', 'Mendes'],
    ['Bruno', 'Ferreira'],
    ['Sofia', 'Gomes'],
    ['Paulo', 'Martins'],
    ['Inês', 'Rocha'],
];

$extraFields = ['LinkedIn', 'GitHub', 'Instagram'];

for ($i = 0; $i < 10; $i++) {
    $first = $nomes[$i][0];
    $last = $nomes[$i][1];
    $fullName = "$first $last";
    $email = strtolower($first . $last) . '@' . strtolower($first . $last) . '.com';
    $password = password_hash('Password123!', PASSWORD_DEFAULT);
    $birth = date('Y-m-d', strtotime("-" . rand(20, 40) . " years"));
    $phone = '91234567' . rand(0, 9);
    $country = 'Portugal';
    $nationality = 'Portuguesa';

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (name, birth_date, nationality, country, email, phone, password, is_verified, email_verified)
                           VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)");
    $stmt->execute([$fullName, $birth, $nationality, $country, $email, $phone, $password]);
    $userId = $pdo->lastInsertId();

    // Insert 2 extra fields
    for ($j = 0; $j < 2; $j++) {
        $fieldName = $extraFields[array_rand($extraFields)];
        $fieldValue = strtolower($first . $last) . "_$fieldName";
        $stmt = $pdo->prepare("INSERT INTO user_extra_fields (user_id, field_name, field_value) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $fieldName, $fieldValue]);
    }

    // Insert article
    $title = "Artigo de $first";
    $image = "IMG_" . ($i + 1) . ".jpg";
    $content = "Este é o conteúdo do artigo de $first $last. Muito interessante e informativo!";
    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, image, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $image, $content]);
}

// Insert 10 notícias
for ($i = 1; $i <= 10; $i++) {
    $titulo = "Notícia nº $i";
    $imagem = "IMG_$i.jpg";
    $texto = "Texto completo da notícia número $i. Informações relevantes para os leitores.";
    $stmt = $pdo->prepare("INSERT INTO noticias (titulo, imagem, texto) VALUES (?, ?, ?)");
    $stmt->execute([$titulo, $imagem, $texto]);
}

echo "Base de dados povoada com sucesso!";