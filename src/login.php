<?php
// login.php atualizado com suporte a redirecionamento pós-login e logout limpo

require_once 'config.php';

// Se houver um parâmetro de redirecionamento, salva na sessão
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

// Se vier de logout, limpa tudo
if (isset($_GET['logout'])) {
    unset($_SESSION['access_token']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_id']);
    unset($_SESSION['redirect_after_login']);
}

// Se não estiver logado, redireciona para o Google OAuth2
if (!isset($_SESSION['access_token'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    echo "entrei";
    exit;
} else {
    // Se já estiver logado, vai para a página desejada ou create_poll
    $redirect = $_SESSION['redirect_after_login'] ?? 'create_poll.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect");
    exit;
}

