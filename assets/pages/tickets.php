<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminpagina.php");
    exit;
}

include_once '../dbinclude/db.php';

$query = $pdo->prepare("SELECT ticket_id, onderwerp, beschrijving, gemaakt_op FROM ticket");
$query->execute();
$tickets = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
    <link href="../css/admin.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
    <div class="sidebar">
            <div class="sidebar-header">Console</div>
            <div class="sidebar-menu">
                <a href="#" class="menu-item"> Dashboard</a>
                <a href="adminpagina.php" class="menu-item"> Gebruikersbeheer</a>
                <a href="#" class="menu-item"> Opslagbeheer</a>
                <a href="tickets.php" class="menu-item"> Tickets</a>
            </div>
            <form action="../includes/logout.php" method="post" class="logout-form">
                <button type="submit" class="logout-button">Uitloggen</button>
            </form>
        </div>

        <div class="main-content">
            <div class="ticket-container">
                <?php foreach ($tickets as $ticket) : ?>
                    <div class="ticket">
                        <div class="ticket-header">
                            <h3><?php echo htmlspecialchars($ticket['onderwerp']); ?></h3>
                        </div>
                        <div class="ticket-body">
                            <p><?php echo nl2br(htmlspecialchars($ticket['beschrijving'])); ?></p>
                            <hr>
                            <p>Gemaakt op: <?php echo htmlspecialchars($ticket['gemaakt_op']); ?></p>
                            <a href="ticketdetails.php?ticket_id=<?php echo htmlspecialchars($ticket['ticket_id']); ?>" class="open-ticket-link">Open Ticket</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>