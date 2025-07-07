<?php
// callback.php atualizado com redirecionamento inteligente pós-login

require_once 'config.php';

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;

    $client->setAccessToken($token);
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    $_SESSION['user_email'] = $userInfo->email;
    $_SESSION['user_name'] = $userInfo->name;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$userInfo->email]);
    $user = $stmt->fetch();

    if (!$user) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$userInfo->name, $userInfo->email]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
    } else {
        $_SESSION['user_id'] = $user['id'];
    }

    $redirect = $_SESSION['redirect_after_login'] ?? 'create_poll.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect");
    exit;
} else {
    echo 'Erro ao autenticar com o Google';
    exit;
}
