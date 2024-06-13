<?php
session_start();
require_once '../dbinclude/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $payment_type = $_POST['payment_type'];

    $stmt = $pdo->prepare('INSERT INTO `order` (user_id, product_id, status) VALUES (?, ?, "In Behandeling")');
    $stmt->execute([$user_id, $product_id]);

    $stmt = $pdo->prepare('INSERT INTO paymentpreference (user_id, type) VALUES (?, ?)');
    $stmt->execute([$user_id, $payment_type]);
    $preference_id = $pdo->lastInsertId();

    if ($payment_type == 'Automatisch Incasso') {
        $iban = $_POST['iban'];
        $account_name = $_POST['account_name'];
        $mandate_date = $_POST['mandate_date'];
        $signature = $_POST['signature'];

        $stmt = $pdo->prepare('INSERT INTO mandate (preference_id, iban, date, account_naam, handtekening) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$preference_id, $iban, $mandate_date, $account_name, $signature]);
    }

    header('Location: ../pages/my_products.php');
    exit;
}
?>
