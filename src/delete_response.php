<?php
// delete_response.php

require_once 'config.php';

$poll_id = $_GET['poll_id'] ?? null;
$email = $_GET['email'] ?? ($_SESSION['user_email'] ?? null);

if (!$poll_id || !$email) {
    echo 'Parâmetros insuficientes.';
    exit;
}

$stmt = $pdo->prepare("DELETE FROM responses WHERE poll_id = ? AND user_email = ?");
$stmt->execute([$poll_id, $email]);

// Obter token da enquete para redirecionar
$stmt = $pdo->prepare("SELECT token FROM polls WHERE id = ?");
$stmt->execute([$poll_id]);
$poll = $stmt->fetch();

if ($poll) {
    header("Location: poll.php?token=" . $poll['token']);
} else {
    echo 'Enquete não encontrada para redirecionamento.';
}
exit;
