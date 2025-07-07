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

$stmt = $pdo->prepare("INSERT INTO responses (poll_id, user_name, user_email, slot_id, available) VALUES (?, ?, ?, ?, ?)");
foreach ($valid_slot_ids as $slot_id) {
    $available = isset($slots[$slot_id]) ? 1 : 0;
    $stmt->execute([$poll_id, $user_name, $user_email, $slot_id, $available]);
}

header("Location: poll.php?token=" . $_GET['token'] ?? '');
exit;
