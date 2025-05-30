<?php
// cria_tabelas.php

// Inclui o ficheiro de conexão à BD
require_once '../includes/db.php';

// SQL para criar as tabelas (coloca tudo numa string)
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    nationality VARCHAR(50),
    country VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    is_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255) DEFAULT NULL,
    login_token VARCHAR(6) DEFAULT NULL,
    login_token_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_extra_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    field_value TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    content TEXT NOT NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_personalizado VARCHAR(255) NOT NULL,
    nome_ficheiro VARCHAR(255) NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS imagem_destaque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caminho VARCHAR(255) NOT NULL,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    visivel BOOLEAN DEFAULT 1
);

CREATE TABLE IF NOT EXISTS atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_atividade ENUM('criação', 'edição', 'artigo_adicionado', 'login', 'logout', 'outro') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS extra_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS user_extra_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    field_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (field_id) REFERENCES extra_fields(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time DATETIME NOT NULL,
    INDEX (email),
    INDEX (ip_address),
    INDEX (attempt_time)
);

CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    status ENUM('success', 'fail'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    comment VARCHAR(100) NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(64) DEFAULT NULL,
    token VARCHAR(64),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    resolvido BOOLEAN DEFAULT FALSE,
    denunciado TINYINT(1) NOT NULL DEFAULT 0 AFTER resolvido,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

UPDATE users 
SET is_verified = 1, verification_token = NULL, is_admin = 1, email_verified = 1
WHERE email = 'admin@admin.com';
SQL;

// Cria o administrador se não existir
try {
    $pdo->exec($sql);
    
    // Verificar se o admin já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => 'admin@admin.com']);
    $adminExiste = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adminExiste) {
        // Criar o admin com password hash
        $passwordAdmin = password_hash('admin', PASSWORD_DEFAULT);
        $stmtInsert = $pdo->prepare("INSERT INTO users (name, birth_date, nationality, country, email, phone, password, is_admin, email_verified, is_verified, status) VALUES (:name, :birth_date, :nationality, :country, :email, :phone, :password, :is_admin, :email_verified, :is_verified, :status)");
        $stmtInsert->execute([
            ':name' => 'Admin',
            ':birth_date' => '2000-01-01',
            ':nationality' => 'PT',
            ':country' => 'Portugal',
            ':email' => 'admin@admin.com',
            ':phone' => '',
            ':password' => $passwordAdmin,
            ':is_admin' => 1,
            ':email_verified' => 1,
            ':is_verified' => 1,
            ':status' => 'ativo',
        ]);
        echo "Tabelas criadas / atualizadas com sucesso.\nAdmin criado com sucesso (admin@admin.com / admin).";
    } else {
        // Se admin já existe, garante que está configurado corretamente (atualiza)
        $stmtUpdate = $pdo->prepare("UPDATE users SET is_admin = 1, email_verified = 1, is_verified = 1 WHERE email = :email");
        $stmtUpdate->execute([':email' => 'admin@admin.com']);
        echo "Tabelas criadas / atualizadas com sucesso.\nAdmin já existia, dados atualizados.";
    }
    
} catch (PDOException $e) {
    echo "Erro ao criar as tabelas: " . $e->getMessage();
}