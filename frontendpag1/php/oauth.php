<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config-google.php';
session_start();

if (isset($_GET['accion'])) {
    $_SESSION['origen'] = $_GET['accion'];
}

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
?>