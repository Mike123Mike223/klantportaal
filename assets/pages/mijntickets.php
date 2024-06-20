<?php
session_start();
require_once '../dbinclude/db.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM ticket WHERE user_id = ?');
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Tickets</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Mijn Tickets</h1>
        <?php if (count($tickets) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Onderwerp</th>
                        <th>Status</th>
                        <th>Aangemaakt op</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= htmlspecialchars($ticket['onderwerp']) ?></td>
                            <td><?= htmlspecialchars($ticket['status']) ?></td>
                            <td><?= htmlspecialchars($ticket['gemaakt_op']) ?></td>
                            <td><a href="ticketdetails_user.php?ticket_id=<?= $ticket['ticket_id'] ?>" class="btn btn-primary">Bekijk</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Je hebt geen tickets.</p>
        <?php endif; ?>
    </div>
</body>
</html>
