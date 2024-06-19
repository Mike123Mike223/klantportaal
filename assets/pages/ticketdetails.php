<?php
session_start();
require_once '../dbinclude/db.php'; // Verbind met de database
require_once '../templates/header.php'; // Header inclusie (indien nodig)

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Haal ticket ID op uit de querystring
if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die('Ongeldige ticket ID.');
}

$ticket_id = $_GET['ticket_id'];
$user_id = $_SESSION['user_id'];


$is_admin = false;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}


if ($is_admin) {
  
    $stmt = $pdo->prepare('SELECT * FROM ticket WHERE ticket_id = ?');
    $stmt->execute([$ticket_id]);
} else {
   
    $stmt = $pdo->prepare('SELECT * FROM ticket WHERE ticket_id = ? AND user_id = ?');
    $stmt->execute([$ticket_id, $user_id]);
}

$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Ticket bestaat niet of je hebt geen toegang tot dit ticket.');
}


$stmt = $pdo->prepare('
    SELECT user_id, admin_id, onderwerp, beschrijving, status, gemaakt_op, update_op
    FROM ticket
    WHERE ticket_id = ?
');
$stmt->execute([$ticket_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Ticket Details</h1>
        <div class="ticket-details">
            <h3>Onderwerp: <?= htmlspecialchars($ticket['onderwerp']) ?></h3>
            <p>Status: <?= htmlspecialchars($ticket['status']) ?></p>
            <p>Aangemaakt op: <?= htmlspecialchars($ticket['gemaakt_op']) ?></p>
            <p>Laatst bijgewerkt: <?= htmlspecialchars($ticket['update_op']) ?></p>
            <p>Beschrijving: <?= nl2br(htmlspecialchars($ticket['beschrijving'])) ?></p>

            <hr>

            
            <?php if (!$is_admin): ?>
            <div class="user-response-form">
                <h4>Reageren</h4>
                <form action="ticketdetails.php?ticket_id=<?= $ticket_id ?>" method="post">
                    <textarea name="user_reply" placeholder="Voeg een reactie toe..." required></textarea>
                    <button type="submit" class="btn btn-primary">Reageren</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once '../templates/footer.php';  ?>
</body>
</html>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_reply']) && !$is_admin) {
    $user_reply = $_POST['user_reply'];

    $update_query = $pdo->prepare("
        UPDATE ticket
        SET beschrijving = CONCAT(beschrijving, '\n\nGebruiker: ', ?, '\n--\n')
        WHERE ticket_id = ?
    ");
    if ($update_query->execute([$user_reply, $ticket_id])) {
       
        header("Refresh:0");
        exit;
    } else {
        die("Er is een fout opgetreden bij het toevoegen van je reactie.");
    }
}
?>
