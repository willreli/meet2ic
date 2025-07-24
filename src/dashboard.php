<?php
// dashboard.php
require_once 'config.php';

if (!isset($_SESSION['access_token']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode('dashboard.php'));
    exit;
}

$user_id   = (int) $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

// Se sua tabela polls ainda não tem created_at, você pode ordenar por id DESC.
$sql = "
    SELECT
        p.id,
        p.title,
        p.token,
        p.created_at,
        (
            SELECT COUNT(*)
            FROM responses r
            WHERE r.poll_id = p.id
        ) AS total_respostas
    FROM polls p
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC, p.id DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Meet2IC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
</head>
<body class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Minhas Enquetes</h2>
            <p class="mb-0 text-muted">
                Logado como <strong><?= htmlspecialchars($user_name) ?></strong>
                <?= $user_email ? '(' . htmlspecialchars($user_email) . ')' : '' ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="create_poll.php" class="btn btn-success">Criar nova enquete</a>
            <a href="logout.php" class="btn btn-outline-danger">Sair</a>
        </div>
    </div>

    <?php if (empty($polls)): ?>
        <div class="alert alert-info">
            Você ainda não criou nenhuma enquete.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Criada em</th>
                        <th>Respostas</th>
                        <th>Link</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($polls as $poll): ?>
                    <tr>
                        <td><?= htmlspecialchars($poll['title']) ?></td>
                        <td>
                            <?php
                              // Caso não tenha created_at, remova esta coluna ou use o id.
                              echo isset($poll['created_at'])
                                  ? date('d/m/Y H:i', strtotime($poll['created_at']))
                                  : '-';
                            ?>
                        </td>
                        <td><?= (int) ($poll['total_respostas'] ?? 0) ?></td>
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   value="<?= 'https://' . $_SERVER['HTTP_HOST'] . '/poll.php?token=' . $poll['token'] ?>"
                                   readonly
                                   onclick="this.select();">
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary"
                               href="poll.php?token=<?= urlencode($poll['token']) ?>"
                               target="_blank">
                                Abrir
                            </a>
                            <!-- Exemplo de ação para apagar (crie o endpoint se quiser)
                            <a class="btn btn-sm btn-outline-danger"
                               href="delete_poll.php?id=<?= (int) $poll['id'] ?>"
                               onclick="return confirm('Tem certeza que deseja apagar esta enquete?');">
                                Excluir
                            </a>
                            -->
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
