<?php
// index.php - Página inicial do Meet2IC

require_once 'config.php';

if (isset($_SESSION['access_token'])) {
    header('Location: create_poll.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Meet2IC - Consulta de Disponibilidade</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .main-container {
      max-width: 600px;
      margin: 100px auto;
      text-align: center;
      padding: 30px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .logo {
      max-width: 200px;
      margin-bottom: 0px;
    }
  </style>
</head>
<body>
  <div class="main-container">
    <img src="images/meet2ic.png" alt="Meet2IC Logo" class="logo">
    <p class="mb-4">Crie e responda enquetes de disponibilidade com facilidade usando sua conta Google.</p>
    <a href="login.php" class="btn btn-primary btn-lg">Entrar com Google</a>
  </div>
</body>
</html>

