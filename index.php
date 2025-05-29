<?php
require_once __DIR__ . '/includes/db.php';

function tabelasExistem(PDO $pdo): bool {
    try {
        $tabelas = ['users']; // podes adicionar outras se quiseres validar mais
        foreach ($tabelas as $tabela) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
            if ($stmt->rowCount() === 0) {
                return false;
            }
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

$tabelasCriadas = tabelasExistem($pdo);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Painel Inicial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        button {
            padding: 12px 25px;
            margin: 10px;
            font-size: 16px;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        #output {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #fff;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            display: flex;
            flex-direction: column;
            white-space: pre-wrap;
            min-height: 60px;
        }
    </style>
</head>
<body>

    <h1>COMUNIDADE DESPORTIVA</h1>
    <h2>Painel de Gestão</h2>
    <p>Este painel permite-te criar e povoar a base de dados e entrar no site.</p>

    <button id="btn-criar" onclick="criarTabelas()" <?= $tabelasCriadas ? 'disabled' : '' ?>>Criar Tabelas (base)</button>
    <button id="btn-seed" onclick="povoarBD()" <?= !$tabelasCriadas ? 'disabled' : '' ?>>Povoar Tabelas</button>
    <button id="btn-aceder" onclick="acederSite()" <?= !$tabelasCriadas ? 'disabled' : '' ?>>Aceder ao Site</button>
    <button onclick="location.reload()">Recarregar Página</button>

    <div id="output">
        Estado: <?= $tabelasCriadas ? '✅ As Tabelas já existem.' : '❌ As Tabelas ainda não foram criadas.' ?>
    </div>

    <script>
        function criarTabelas() {
            const output = document.getElementById('output');
            output.textContent = "🛠️ A criar as tabelas...";

            fetch('seedBD/criar_tabelas.php')
                .then(response => response.text())
                .then(data => {
                    output.textContent = data;
                    if (data.toLowerCase().includes("sucesso")) {
                        document.getElementById('btn-aceder').disabled = false;
                        document.getElementById('btn-seed').disabled = false;
                        document.getElementById('btn-criar').disabled = true;
                    } else {
                        output.textContent = "❌ Erro ao criar as tabelas:\n\n" + data;
                    }
                })
                .catch(error => {
                    output.textContent = "❌ Erro de ligação:\n\n" + error;
                });
        }

        function povoarBD() {
            const output = document.getElementById('output');
            output.textContent = "🌱 A povoar as tabelas...";

            fetch('seedBD/povoar_tabelas.php')
                .then(response => response.text())
                .then(data => {
                    output.textContent = data;
                    document.getElementById('btn-seed').disabled = true; // 👈 aqui!
                })
                .catch(error => {
                    output.textContent = "❌ Erro ao povoar a base de dados:\n\n" + error;
                });
        }

        function acederSite() {
            window.location.href = 'public/index.php';
        }
    </script>

</body>
</html>