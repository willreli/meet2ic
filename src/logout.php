<?php
// logout.php

session_start();

// Remove dados da sessão individualmente
unset($_SESSION['access_token']);
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);
unset($_SESSION['user_id']);
unset($_SESSION['redirect_after_login']);

// Destrói a sessão inteira
session_destroy();

// Redireciona para login com sinalizador de logout
// header('Location: login.php?logout=1');
header('Location: index.php?logout=1');
exit;

