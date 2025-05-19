-- Criação da base de dados
CREATE DATABASE comunidade_desportiva DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comunidade_desportiva;

-- Tabela principal de utilizadores
CREATE TABLE users (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de campos extra (para uso do admin)
CREATE TABLE user_extra_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    field_value TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tokens de verificação de email (valorização)
CREATE TABLE email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- tabela de artigos
CREATE TABLE articles (
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

-- Tabela de documentos
CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_personalizado VARCHAR(255) NOT NULL,
    nome_ficheiro VARCHAR(255) NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- tabela de imagens em destaque
CREATE TABLE imagem_destaque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caminho VARCHAR(255) NOT NULL,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de notícias
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN status ENUM('ativo', 'inativo') DEFAULT 'ativo';

CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_atividade ENUM('criação', 'edição', 'artigo_adicionado', 'login', 'logout', 'outro') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE noticias ADD COLUMN visivel BOOLEAN DEFAULT 1;

ALTER TABLE users 
ADD COLUMN is_verified TINYINT(1) DEFAULT 0,
ADD COLUMN verification_token VARCHAR(255) DEFAULT NULL;


UPDATE users 
SET is_verified = 1, verification_token = NULL 
WHERE email = 'admin@admin.com';

ALTER TABLE users 
ADD COLUMN login_token VARCHAR(6) DEFAULT NULL,
ADD COLUMN login_token_expires DATETIME DEFAULT NULL;