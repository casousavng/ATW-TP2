<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin(); // Verifica se o utilizador é admin

$errors = [];
$success = false;

// Adicionar novo campo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_field'])) {
    $field_name = trim($_POST['field_name'] ?? '');
    $field_type = trim($_POST['field_type'] ?? 'texto');

    if (empty($field_name)) {
        $errors[] = "O nome do campo é obrigatório.";
    } else {
        // Verificar duplicação
        $stmt = $pdo->prepare("SELECT * FROM extra_fields WHERE name = ?");
        $stmt->execute([$field_name]);

        if ($stmt->fetch()) {
            $errors[] = "Já existe um campo com este nome.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO extra_fields (name, type) VALUES (?, ?)");
            $stmt->execute([$field_name, $field_type]);
            $success = true;
        }
    }
}

// Remover campo
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $field_id = $_GET['delete'];

    // Remover diretamente da tabela extra_fields
    $stmt = $pdo->prepare("DELETE FROM extra_fields WHERE id = ?");
    $stmt->execute([$field_id]);
    $success = true;
}

// Buscar todos os campos existentes
$stmt = $pdo->query("SELECT * FROM extra_fields ORDER BY name ASC");
$fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestão de Campos Extra">
    <title>Gestão de Campos Extra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .text_center {
            text-align: center;
        }

        th, td {
            text-align: center;
            vertical-align: middle;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 576px) {
            h2, h5 {
                font-size: 1.25rem;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.4rem 0.6rem;
            }

            .form-control, .form-select {
                font-size: 0.9rem;
            }

            .modal-body, .modal-footer, .modal-title {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <a href="../index.php" class="btn btn-outline-secondary mb-4">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <h2 class="mb-4">Gestão de Campos Extra</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">Operação realizada com sucesso.</div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <!-- Formulário para adicionar novo campo -->
        <form method="POST" class="bg-white p-4 shadow rounded mb-4">
            <h5 class="mb-3">Adicionar Novo Campo</h5>
            <div class="mb-3">
                <label for="field_name" class="form-label">Nome do Campo</label>
                <input type="text" class="form-control" id="field_name" name="field_name" required>
            </div>
            <div class="mb-3">
                <label for="field_type" class="form-label">Tipo</label>
                <select name="field_type" id="field_type" class="form-select">
                    <option value="texto">Texto</option>
                    <option value="numero">Número</option>
                    <option value="url">URL</option>
                </select>
            </div>
            <button type="submit" name="add_field" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Adicionar Campo
            </button>
        </form>

        <!-- Lista de campos existentes -->
        <h5 class="mb-3">Campos Existentes</h5>
        <div class="table-responsive">
            <table class="table table-bordered bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fields as $field): ?>
                        <tr>
                            <td><?= $field['id'] ?></td>
                            <td><?= htmlspecialchars($field['name']) ?></td>
                            <td><?= htmlspecialchars($field['type']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal"
                                        data-field-id="<?= $field['id'] ?>"
                                        data-field-name="<?= htmlspecialchars($field['name']) ?>">
                                    <i class="bi bi-trash"></i> Remover
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">
                <i class="bi bi-exclamation-triangle text-warning"></i> Confirmar Remoção
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            Tem a certeza que deseja remover o campo <strong id="modalFieldName">este campo</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle"></i> Cancelar
            </button>
            <a href="#" class="btn btn-danger" id="confirmDeleteBtn">
                <i class="bi bi-trash"></i> Remover
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS + Script Modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('confirmDeleteModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const fieldId = button.getAttribute('data-field-id');
            const fieldName = button.getAttribute('data-field-name');

            const modalFieldName = modal.querySelector('#modalFieldName');
            const confirmDeleteBtn = modal.querySelector('#confirmDeleteBtn');

            modalFieldName.textContent = fieldName;
            confirmDeleteBtn.href = `?delete=${fieldId}`;
        });
    });
    </script>
</body>
</html>