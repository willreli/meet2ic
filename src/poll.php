<?php
// poll.php com nomes e horários escolhidos por cada pessoa

require_once 'config.php';

if (!isset($_GET['token'])) {
    echo 'Token da enquete não fornecido.';
    exit;
}

$token = $_GET['token'];
$just_created = isset($_GET['created']);

$stmt = $pdo->prepare("SELECT * FROM polls WHERE token = ?");
$stmt->execute([$token]);
$poll = $stmt->fetch();

if (!$poll) {
    echo 'Enquete não encontrada.';
    exit;
}

$stmt_slots = $pdo->prepare("SELECT * FROM poll_slots WHERE poll_id = ? ORDER BY start_time ASC");
$stmt_slots->execute([$poll['id']]);
$slots = $stmt_slots->fetchAll();

$slot_map = [];
foreach ($slots as $s) {
    $slot_map[$s['id']] = $s;
}

$stmt_summary = $pdo->prepare("
    SELECT slot_id, user_name, available
    FROM responses
    WHERE poll_id = ?
");
$stmt_summary->execute([$poll['id']]);

$resumo = [];
foreach ($stmt_summary as $row) {
    $slot_id = $row['slot_id'];
    $status = (int)$row['available'];
    $name = $row['user_name'];

    if (!isset($resumo[$slot_id])) {
        $resumo[$slot_id] = [
            'disponiveis' => [],
            'talvez' => []
        ];
    }

    if ($status === 1) {
        $resumo[$slot_id]['disponiveis'][] = $name;
    } elseif ($status === 0) {
        $resumo[$slot_id]['talvez'][] = $name;
    }
}

$stmt_names = $pdo->prepare("SELECT DISTINCT user_name, user_email FROM responses WHERE poll_id = ? ORDER BY user_name ASC");
$stmt_names->execute([$poll['id']]);
$user_names = $stmt_names->fetchAll();

$stmt_details = $pdo->prepare("SELECT user_name, user_email, slot_id, available FROM responses WHERE poll_id = ? AND (available = 1 OR available =0 OR available = -1)");
$stmt_details->execute([$poll['id']]);
$responses_by_user = [];
$responses_by_user_email = [];
foreach ($stmt_details as $row) {
    $responses_by_user[$row['user_name']][] = $row['slot_id'];
    $responses_by_user_email[$row['user_name']]['email'] = $row['user_email'];
}

$stmt_details = $pdo->prepare("SELECT user_name, user_email, slot_id, available FROM responses WHERE poll_id = ? AND (available = 1 OR available =0)");
$stmt_details->execute([$poll['id']]);
$responses_by_user_available = [];
foreach ($stmt_details as $row) {
    $responses_by_user_available[$row['user_name']][] = $row['slot_id'];
}

// Verifica quem marcou tudo como indisponível
$stmt_all_negative = $pdo->prepare("
    SELECT user_name, COUNT(*) AS total_indisp
    FROM responses
    WHERE poll_id = ? AND available = -1
    GROUP BY user_name
");
$stmt_all_negative->execute([$poll['id']]);

$usuarios_tudo_indisponivel = [];
foreach ($stmt_all_negative as $row) {
    if ((int)$row['total_indisp'] === count($slots)) {
        $usuarios_tudo_indisponivel[] = $row['user_name'];
    }
}

$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

// Passo 1: Determinar o melhor slot (maior 'disponiveis', e em caso de empate, maior 'talvez')
$max_disponiveis = -1;
$max_talvez = -1;
$melhor_slot_id = null;

foreach ($slots as $slot) {
    $r = $resumo[$slot['id']] ?? ['disponiveis' => [], 'talvez' => []];
    $qtd_disponiveis = count($r['disponiveis']);
    $qtd_talvez = count($r['talvez']);

    if (
        $qtd_disponiveis > $max_disponiveis ||
        ($qtd_disponiveis === $max_disponiveis && $qtd_talvez > $max_talvez)
    ) {
        $max_disponiveis = $qtd_disponiveis;
        $max_talvez = $qtd_talvez;
        $melhor_slot_id = $slot['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Responder Enquete</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
.melhor-horario {
  background-color: #d4edda; /* verde claro */
  font-weight: bold;
}
</style>

</head>
<body class="container mt-5">

<nav class="mb-4 d-flex justify-content-end">
  <a class="btn btn-primary me-2" href="./create_poll.php">Crie sua enquete</a>
  <?php if ($user_name): ?>
  <a href="dashboard.php" class="btn btn-success me-2">Minhas enquetes</a>
  <a class="btn btn-outline-danger" href="./logout.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Sair</a>
  <?php endif; ?>
</nav>

    <h2 style="color: blue">Enquete: <?= htmlspecialchars($poll['title']) ?></h2>

    <?php if (!empty($poll['description'])): ?>
    <div class="alert alert-info">
	<label>Descrição da enquete:</label>
        <?= $poll['description'] ?>
    </div>
    <?php endif; ?>


    <?php if ($just_created): ?>
        <div class="alert alert-success">
            <strong>Enquete criada com sucesso!</strong><br>
            Compartilhe este link com os participantes:<br>
            <input type="text" class="form-control mt-2" value="<?= 'https://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '&') ?>" readonly onclick="this.select();">
        </div>
    <?php endif; ?>

    <?php if (!$user_name): ?>
        <div class="alert alert-warning">
            <p>Você ainda não está logado (entre com sua conta para facilitar o preenchimento). <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-sm btn-outline-primary">Entrar com Google</a></p>
        </div>
    <?php endif; ?>

    <hr>
    <form action="submit_availability.php?token=<?= $token ?>" method="POST">
        <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">

        <?php if (!$user_name): ?>
        <div class="mb-3">
            <h4>Marque sua disponibilidade e suas informações.</h4>
            <input type="text" name="user_name" class="form-control" placeholder="Entre com seu nome" required> <br>
            <!-- <label for="user_email" class="form-label">Seu e-mail:</label> -->
            <input type="email" name="user_email" class="form-control" placeholder="Entre com seu e-mail" required>
        </div>
        <?php else: ?>
            <h3>Marque sua disponibilidade.</h3>
            <input type="hidden" name="user_name" value="<?= htmlspecialchars($user_name) ?>">
            <input type="hidden" name="user_email" value="<?= htmlspecialchars($user_email) ?>">
            <p><strong>Você está respondendo como:</strong> <?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($user_email) ?>)</p>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Horário</th>
                    <th>Disponível ?</th>
                    <th>Talvez ?</th>
                    <th>Indisponível ?</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slots as $slot): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($slot['start_time'])) ?> às <?= date('H:i', strtotime($slot['end_time'])) ?></td>
                    <td><input type="radio" name="slots[<?= $slot['id'] ?>]" value="1"></td>
                    <td><input type="radio" name="slots[<?= $slot['id'] ?>]" value="0"></td>
                    <td><input type="radio" name="slots[<?= $slot['id'] ?>]" value="-1" checked></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">Salvar Disponibilidade</button>
        <?php if ($user_name): ?>
            <a href="delete_response.php?poll_id=<?= $poll['id'] ?>&email=<?= urlencode($user_email) ?>" class="btn btn-danger ms-2">Apagar Respostas</a>
        <?php endif; ?>
    </form>

<hr>

<table style="border-collapse: separate; border-spacing: 10px;">
<tr>
<td>
<h4 class="mt-4">Resumo das Respostas:</h4>
<table style="border-collapse: separate; border-spacing: 10px;">
  <?php foreach ($slots as $slot): ?>
    <?php
      $r = $resumo[$slot['id']] ?? ['disponiveis' => [], 'talvez' => []];
      if (count($r['disponiveis']) === 0 && count($r['talvez']) === 0) continue;

      $text_disponiveis = '<b>'. count($r['disponiveis']) . ' disponível(is)</b>';
      if ($r['disponiveis']) {
          $text_disponiveis .= ' (' . implode(' / ', $r['disponiveis']) . ')';
      }

      $text_talvez = count($r['talvez']) . ' talvez';
      if ($r['talvez']) {
          $text_talvez .= ' (' . implode(' / ', $r['talvez']) . ')';
      }

      $highlight_class = ($slot['id'] === $melhor_slot_id) ? 'melhor-horario' : '';
    ?>
    <tr class="<?= $highlight_class ?>">
      <td> <?= date('d/m/Y H:i', strtotime($slot['start_time'])) ?> às <?= date('H:i', strtotime($slot['end_time'])) ?> </td>
      <td> <?= $text_disponiveis ?> <br> <?= $text_talvez ?> </td>
    </tr>
  <?php endforeach; ?>
</table>
</td>
<td>
<h5 class="mt-4">Quem já respondeu ?</h5>
<ul>
<?php foreach ($responses_by_user as $name => $slot_ids): ?>
    <?php
        $nome_formatado = htmlspecialchars($name);
        $email = htmlspecialchars($responses_by_user_email["$name"]['email']);
        $classe = in_array($name, $usuarios_tudo_indisponivel) ? 'text-danger' : '';
        $comentario = in_array($name, $usuarios_tudo_indisponivel) ? ' (indisponível para todos os horários)' : '';
    ?>
    <li class="<?= $classe ?>"><strong><?= $nome_formatado ?></strong> - (<?= $email ?>) <?= $comentario ?></li>
<?php endforeach; ?>
    <?php if (empty($responses_by_user)): ?>
        <li>Ninguém respondeu ainda...</li>
    <?php endif; ?>
</ul>
</td>
</tr>
</table>

<hr>

    <h5 class="mt-4">Disponibilidade dos Participantes:</h5>
<ul>
<?php foreach ($responses_by_user as $name => $slot_ids): ?>
    <?php
        $nome_formatado = htmlspecialchars($name);
        $email = isset($responses_by_user_email[$name]['email']) 
            ? htmlspecialchars($responses_by_user_email[$name]['email']) 
            : '';
        $tudo_indisponivel = in_array($name, $usuarios_tudo_indisponivel);
        $classe = $tudo_indisponivel ? 'text-danger' : '';
	$comentario = $tudo_indisponivel ? ' <em>(indisponível para todos os horários)</em>' : '';
	$slot_ids_available = $responses_by_user_available[$name] ?? [];
    ?>
    <li class="<?= $classe ?>">
        <strong><?= $nome_formatado ?></strong>
        <?php if ($email): ?> – (<?= $email ?>)<?php endif; ?>
	<?= $comentario ?>
	          <ul>
                     <?php foreach ($slot_ids_available as $id): ?>
                         <?php $slot = $slot_map[$id]; ?>
                         <li><?= date('d/m/Y H:i', strtotime($slot['start_time'])) ?> às <?= date('H:i', strtotime($slot['end_time'])) ?></li>
                     <?php endforeach; ?>
                 </ul>
    </li>
<?php endforeach; ?>

<?php if (empty($responses_by_user)): ?>
    <li>Ninguém respondeu ainda.</li>
<?php endif; ?>
</ul>
</body>
</html>

