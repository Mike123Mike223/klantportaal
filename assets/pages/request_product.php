<?php
session_start();
require_once '../dbinclude/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controleer of alle vereiste velden zijn ontvangen
    if (!isset($_POST['product_id'], $_POST['payment_type'])) {
        $_SESSION['error_message'] = 'Alle vereiste velden moeten worden ingevuld.';
        header('Location: /pages/products.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $payment_type = $_POST['payment_type'];
    $iban = isset($_POST['iban']) ? $_POST['iban'] : null;
    $account_name = isset($_POST['account_name']) ? $_POST['account_name'] : null;
    $signature = isset($_POST['signature']) ? $_POST['signature'] : null;
    $status = 'In Behandeling';
    $registratie_datum = date('Y-m-d'); // Formaat: YYYY-MM-DD
    $verloop_datum = date('Y-m-d', strtotime('+1 year')); 

    // SLA heeft een andere verloopdatum, stel hier indien nodig
    $stmt = $pdo->prepare('SELECT type FROM product WHERE product_id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $verloop_datum = null; // Default waarde
    if ($product['type'] === 'SLA') {
        $verloop_datum = null; // SLA heeft geen verloopdatum
    } else {
        $verloop_datum = date('Y-m-d', strtotime('+1 year'));
    }

    // Voeg een nieuwe bestelling toe met de status "In Behandeling"
    $stmt = $pdo->prepare('INSERT INTO `order` (user_id, product_id, bestelling_datum, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$user_id, $product_id, $registratie_datum, $status]);
    $order_id = $pdo->lastInsertId();

    // Voeg een nieuwe betalingsvoorkeur toe
    $stmt = $pdo->prepare('INSERT INTO paymentpreference (user_id, type) VALUES (?, ?)');
    $stmt->execute([$user_id, $payment_type]);
    $preference_id = $pdo->lastInsertId();

    // Voeg een nieuw mandaat toe als de betalingsvoorkeur "Automatisch Incasso" is
    if ($payment_type === 'Automatisch Incasso') {
        $mandate_date = isset($_POST['mandate_date']) ? $_POST['mandate_date'] : null;

        $stmt = $pdo->prepare('INSERT INTO mandate (preference_id, iban, datum, account_naam, handtekening) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$preference_id, $iban, $mandate_date, $account_name, $signature]);
    }

    // Redirect naar de productenpagina met succesmelding
    $_SESSION['success_message'] = 'Uw productaanvraag is ingediend en wordt nu verwerkt.';
    header('Location: /pages/products.php');
    exit;
}
?>
