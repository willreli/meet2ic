<?php
// create_poll.php com FullCalendar 6.1.10 (modo global corrigido e tratamento de timezone)

require_once 'config.php';

if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit;
}

$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $selected_blocks = json_decode($_POST['selected_blocks'], true);

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO polls (user_id, title, token, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $token, $description]);
    $poll_id = $pdo->lastInsertId();

    $stmt_slot = $pdo->prepare("INSERT INTO poll_slots (poll_id, start_time, end_time) VALUES (?, ?, ?)");
    foreach ($selected_blocks as $block) {
        $start = preg_replace('/(\+|\-)\d{2}:\d{2}$/', '', $block['start']);
        $end = preg_replace('/(\+|\-)\d{2}:\d{2}$/', '', $block['end']);
        $stmt_slot->execute([$poll_id, $start, $end]);
    }

    header("Location: poll.php?token=$token&created=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Criar Enquete</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
  <script src="./ckeditor/ckeditor.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('description')) {
      CKEDITOR.replace('description');
    }
  });
</script>

  <style>
    #calendar {
      max-width: 1000px;
      max-height: 500px;
      margin: 40px auto;
    }
  </style>
</head>
<body class="container mt-5">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2>Criar Enquete de Disponibilidade</h2>
      <p class="text-muted">Logado como <strong><?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($user_email) ?>)</strong></p>
    </div>

<nav class="mb-4 d-flex justify-content-end">
  <a href="dashboard.php" class="btn btn-success me-2">Minhas enquetes</a>
  <a href="logout.php" class="btn btn-outline-danger">Sair</a>
</nav>


  </div>

  <form method="POST" onsubmit="return prepareSubmission();">
    <div class="mb-3">
      <label class="form-label"><b>Título da Enquete:</b></label>
      <input type="text" name="title" class="form-control" required>
      <label for="description">Descrição da Enquete:</label>
      <textarea name="description" id="description" class="form-control" rows="5"></textarea>
<hr>
      <p class="text-muted">Clique (segure) e arraste para selecionar os horários desejado. Clique em um horário 'Alocado' para removê-lo.</p>
    </div>

    <input type="hidden" name="selected_blocks" id="selected_blocks">
    <div id='calendar'></div>
    <div class="text-center">
      <button type="submit" class="btn btn-primary mt-3">Criar Enquete</button>
    </div>
  </form>

  <script>
    let selectedBlocks = [];

    document.addEventListener('DOMContentLoaded', function () {
      let calendarEl = document.getElementById('calendar');
      let calendar = new window.FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        selectable: true,
        editable: false,
        allDaySlot: false,
        slotDuration: '00:30:00',
        locale: 'pt-br',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'timeGridWeek,timeGridDay'
        },
        select: function (info) {
          selectedBlocks.push({
            start: info.startStr.replace('T', ' '),
            end: info.endStr.replace('T', ' ')
          });
          calendar.addEvent({
            start: info.start,
  	    end: info.end,
	    title: 'Alocado',
            //display: 'background',
            backgroundColor: '#0d6efd',
            borderColor: '#0d6efd'
          });
	},

	  // Quando clica em um horário já selecionado
	  eventClick: function(info) {
	    if (confirm('Deseja remover este horário?')) {
	      info.event.remove();

	      // Remove também do array
	      selectedBlocks = selectedBlocks.filter(b =>
		!(b.start === info.event.startStr.replace('T', ' ') &&
		  b.end === info.event.endStr.replace('T', ' '))
	      );
	    }
	  }

      });
      calendar.render();
    });

    function prepareSubmission() {
      if (selectedBlocks.length === 0) {
        alert('Selecione pelo menos um horário no calendário.');
        return false;
      }
      document.getElementById('selected_blocks').value = JSON.stringify(selectedBlocks);
      return true;
    }
  </script>
</body>
</html>

