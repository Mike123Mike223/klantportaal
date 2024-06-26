<?php
session_start();
require_once '../dbinclude/db.php'; 
require_once '../templates/header.php'; 


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: /login.php');
    exit;
}


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM ticket WHERE user_id = ? ORDER BY gemaakt_op DESC');
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Mijn Tickets</h1>
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
                        <td>
                            <a href="ticketdetails.php?ticket_id=<?= $ticket['ticket_id'] ?>" class="btn btn-primary btn-sm">Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../templates/footer.php';  ?>
</body>
</html>
