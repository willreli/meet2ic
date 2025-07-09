<?php
// Estrutura inicial do projeto (arquivos PHP)
// /config.php
// /login.php
// /callback.php
// /create_poll.php
// /poll.php
// /submit_availability.php
// /db.sql

// Este é o esqueleto do arquivo config.php

session_start();

$fqdn = 'localhost';
$maildomain = 'localhost';

$clientID = 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com';
$clientSecret = 'XXXXXX-XXXXXXXXXXXXXXXXXXXXXXX_XXX-';
$redirectUri = 'http://localhost:8080/callback.php';

$host = 'mariadb';
$db   = 'ddic';
$user = 'uddic';
$pass = 'S3nh4@25u';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     throw new PDOException($e->getMessage(), (int)$e->getCode());
}

require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

