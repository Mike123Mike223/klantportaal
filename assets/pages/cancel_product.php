<?php
session_start();
require_once '../dbinclude/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];

    // Controleer of de gebruiker de eigen bestelling opzegt
    $stmt = $pdo->prepare('SELECT * FROM `order` WHERE order_id = ? AND user_id = ?');
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['error_message'] = 'U heeft geen toestemming om deze bestelling op te zeggen.';
        header('Location: ../pages/products.php');
        exit;
    }

    // Voer de opzegging uit
    $stmt = $pdo->prepare('UPDATE `order` SET status = "Opgezegd" WHERE order_id = ?');
    $stmt->execute([$order_id]);

    // Redirect naar de productenpagina met succesmelding
    $_SESSION['success_message'] = 'Uw product is succesvol opgezegd.';
    header('Location: ../pages/products.php');
    exit;
}
?>
