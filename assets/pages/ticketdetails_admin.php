<?php
session_start();
require_once '../dbinclude/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$admin_id = $_SESSION['user_id'];


if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die('Ongeldige ticket ID.');
}

$ticket_id = $_GET['ticket_id'];


$stmt = $pdo->prepare('SELECT * FROM ticket WHERE ticket_id = ?');
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Ticket bestaat niet.');
}

// Haal alle berichten op voor ticket op 
$stmt = $pdo->prepare('
    SELECT b.bericht_id, b.ticket_id, b.user_id, b.admin_id, 
    b.bericht, b.gemaakt_op, u.naam AS gebruiker_naam
    FROM berichten b
    LEFT JOIN user u ON b.user_id = u.user_id
    WHERE b.ticket_id = ?
    ORDER BY b.gemaakt_op ASC
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
    <link href="../css/ticket.css" rel="stylesheet">
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

            <div class="messages">
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= $message['admin_id'] ? 'admin-message' : 'user-message' ?>">
                        <p><strong><?= $message['admin_id'] ? 'Admin' : $message['gebruiker_naam'] ?>:</strong> <?= htmlspecialchars($message['bericht']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

           
            <div class="admin-response-form">
                <h4>Reageren</h4>
                <form action="ticketdetails_admin.php?ticket_id=<?= $ticket_id ?>" method="post">
                    <textarea name="admin_reply" placeholder="Voeg een reactie toe..." required></textarea>
                    <button type="submit" class="btn btn-primary">Reageren</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Verwerk admin reactie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_reply'])) {
    $admin_reply = $_POST['admin_reply'];

    // Voeg reactie toe aan de berichten tabel
    $insert_query = $pdo->prepare("
        INSERT INTO berichten (ticket_id, admin_id, bericht, gemaakt_op)
        VALUES (?, ?, ?, NOW())
    ");
    if ($insert_query->execute([$ticket_id, $admin_id, $admin_reply])) {
        // refresh pagina om updates te laten zien
        header("Refresh:0");
        exit;
    } else {
        die("Er is een fout opgetreden bij het toevoegen van je reactie.");
    }
}
?>
