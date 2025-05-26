<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '../../includes/db.php');
require_once(BASE_PATH . '../../includes/auth.php');
checkAdmin();

$table = $_GET['t'] ?? 'logs';
$perPage = 20;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$params = [];
$filters = "";

// Filtros
if (!empty($_GET['email'])) {
    $filters .= " AND email LIKE :email";
    $params[':email'] = '%' . $_GET['email'] . '%';
}

if (!empty($_GET['ip'])) {
    $filters .= " AND ip_address LIKE :ip";
    $params[':ip'] = '%' . $_GET['ip'] . '%';
}

if (!empty($_GET['from'])) {
    $filters .= " AND created_at >= :from";
    $params[':from'] = $_GET['from'];
}

if (!empty($_GET['to'])) {
    $filters .= " AND created_at <= :to";
    $params[':to'] = $_GET['to'];
}

if ($table === 'logs' && !empty($_GET['status'])) {
    $filters .= " AND status = :status";
    $params[':status'] = $_GET['status'];
}

// Paginação
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM " . ($table === 'attempts' ? 'login_attempts' : 'login_logs') . " WHERE 1=1 $filters");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Dados
$sql = "SELECT * FROM " . ($table === 'attempts' ? 'login_attempts' : 'login_logs') . " WHERE 1=1 $filters ORDER BY " . ($table === 'attempts' ? 'attempt_time' : 'created_at') . " DESC LIMIT $offset, $perPage";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exportar CSV
if (isset($_GET['export']) && $_GET['export'] === '1') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=' . $table . '_export.csv');
    $out = fopen('php://output', 'w');

    if ($table === 'attempts') {
        fputcsv($out, ['Email', 'IP', 'Data/Hora']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['email'], $r['ip_address'], $r['attempt_time']]);
        }
    } else {
        fputcsv($out, ['Email', 'IP', 'Status', 'Data/Hora']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['email'], $r['ip_address'], $r['status'], $r['created_at']]);
        }
    }
    exit;
}

// Gerar link exportação com segurança
$exportLink = '?t=' . urlencode($table) . '&export=1';
if (!empty($_GET)) {
    $params = $_GET;
    unset($params['export']);
    $exportLink .= '&' . http_build_query($params);
}

// Função para verificar se IP é privado
function isPrivateIP($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
}

// Função simples para pegar código de país por IP — aqui simulo só com regex (você pode ampliar ou integrar API real)
function getCountryCodeFromIP($ip) {
    // IP privados
    if (!filter_var($ip, FILTER_VALIDATE_IP)) return '';
    if (!isPrivateIP($ip)) return '';

    // Simulação: para IPs públicos, um lookup básico (exemplo fixo)
    // Aqui você pode integrar uma API externa ou base de dados geolocalização.
    // Vou colocar só exemplo com 2 IPs:
    $known = [
        '8.8.8.8' => 'us',
        '1.1.1.1' => 'au',
    ];
    return $known[$ip] ?? '';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Gestao de Acessos - Comunidade Desportiva" />
    <meta name="keywords" content="Gestao de Acessos, Comunidade Desportiva" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon" />
    <title>Gestão de Acessos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Flag icons CSS: https://github.com/lipis/flag-icon-css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icon.min.css" rel="stylesheet" />
    <style>
        .flag-icon {
            margin-right: 6px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    <h1 class="mb-4">Gestão de Acessos</h1>

    <form method="get" class="row g-2 mb-4">
        <input type="hidden" name="t" value="<?= htmlspecialchars($table) ?>">
        <div class="col-md-2">
            <input type="text" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="ip" class="form-control" placeholder="IP" value="<?= htmlspecialchars($_GET['ip'] ?? '') ?>">
        </div>
        <?php if ($table === 'logs'): ?>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Selecione o Status</option>
                    <option value="success" <?= ($_GET['status'] ?? '') === 'success' ? 'selected' : '' ?>>Sucesso</option>
                    <option value="fail" <?= ($_GET['status'] ?? '') === 'fail' ? 'selected' : '' ?>>Falha</option>
                </select>
            </div>
        <?php endif; ?>
        <div class="col-md-2">
            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
        </div>
        <div class="col-md-2 d-flex">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <a href="<?= $exportLink ?>" class="btn btn-success">
                <i class="bi bi-download"></i> Exportar CSV
            </a>
        </div>
    </form>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $table === 'logs' ? 'active' : '' ?>" href="?t=logs">Registos de Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $table === 'attempts' ? 'active' : '' ?>" href="?t=attempts">Tentativas de Intrusão</a>
        </li>
    </ul>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="<?= $table === 'attempts' ? 'table-danger' : 'table-info' ?>">
            <tr>
                <th>Email</th>
                <th>IP</th>
                <?php if ($table === 'logs'): ?><th>Status</th><?php endif; ?>
                <th>Data/Hora</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <?php
                        $ip = $row['ip_address'];
                        $code = getCountryCodeFromIP($ip);
                        if ($code):
                            // Mostra bandeira do país (usa flag-icon-css)
                            echo '<span class="flag-icon flag-icon-' . htmlspecialchars($code) . '"></span> ';
                        else:
                            // Ícone padrão (globo) para IP privado ou desconhecido
                            echo '<i class="bi bi-globe"></i> ';
                        endif;
                        echo htmlspecialchars($ip);
                        ?>
                    </td>
                    <?php if ($table === 'logs'): ?>
                        <td>
                            <?php
                            $statusText = ($row['status'] === 'success') ? 'Sucesso' : (($row['status'] === 'fail') ? 'Falha' : htmlspecialchars($row['status']));
                            ?>
                            <span class="badge <?= $row['status'] === 'success' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $statusText ?>
                            </span>
                        </td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($table === 'attempts' ? $row['attempt_time'] : $row['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo '<i class="bi bi-globe"></i> Nota: IPs privados não apresentam bandeira.'; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>