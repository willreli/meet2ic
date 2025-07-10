<?php
// submit_availability.php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Requisição inválida.';
    exit;
}

$poll_id = $_POST['poll_id'] ?? null;
$user_name = $_POST['user_name'] ?? ($_SESSION['user_name'] ?? null);
$user_email = $_POST['user_email'] ?? ($_SESSION['user_email'] ?? null);
$slots = $_POST['slots'] ?? [];

if (!$poll_id || !$user_name) {
    echo 'Dados incompletos.';
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM poll_slots WHERE poll_id = ?");
$stmt->execute([$poll_id]);
$valid_slot_ids = array_column($stmt->fetchAll(), 'id');

// Salvar respostas
$stmt = $pdo->prepare("INSERT INTO responses (poll_id, user_name, user_email, slot_id, available) VALUES (?, ?, ?, ?, ?)");
foreach ($slots as $slot_id => $status) {
    if (in_array($status, ['1', '0', '-1'])) { // só grava disponível (1) ou talvez (0)
        $stmt->execute([$poll_id, $user_name, $user_email, $slot_id, $status]);
    }
}

#$stmt = $pdo->prepare("INSERT INTO responses (poll_id, user_name, user_email, slot_id, available) VALUES (?, ?, ?, ?, ?)");
#foreach ($valid_slot_ids as $slot_id) {
    #$available = isset($slots[$slot_id]) ? 1 : 0;
    #$stmt->execute([$poll_id, $user_name, $user_email, $slot_id, $available]);
#}

// Obter dados do criador da enquete
$stmt = $pdo->prepare("
  SELECT u.email, u.name, p.title, p.token 
  FROM polls p 
  JOIN users u ON p.user_id = u.id 
  WHERE p.id = ?
");
$stmt->execute([$poll_id]);
$creator = $stmt->fetch();

if ($creator && filter_var($creator['email'], FILTER_VALIDATE_EMAIL)) {
  $responder_name = $_SESSION['user_name'] ?? $user_name;

  $to = $creator['email'];
  $subject = "[Meet2IC] $responder_name respondeu à sua enquete";
  $message = "Olá {$creator['name']},\n\n" .
             "$responder_name acabou de responder à sua enquete \"{$creator['title']}\".\n\n" .
             "Acesse: https://$fqdn/poll.php?token={$creator['token']}\n\n--\nMeet2IC";
  $headers = "From: noreply@$maildomain";

  mail($to, $subject, $message, $headers);
}

header("Location: poll.php?token=" . $_GET['token'] ?? '');
exit;
