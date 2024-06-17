<?php
session_start();
include_once '../dbinclude/db.php'; 

if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    header("Location: tickets.php");
    exit;
}

$ticket_id = $_GET['ticket_id'];

$query = $pdo->prepare("SELECT * FROM ticket WHERE ticket_id = ?");
$query->execute([$ticket_id]);
$ticket = $query->fetch(PDO::FETCH_ASSOC);


if (!$ticket) {
    
    header("Location: tickets.php");
    exit;
}


$user_query = $pdo->prepare("SELECT naam, email FROM user WHERE user_id = ?");
$user_query->execute([$ticket['user_id']]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <link href="../css/admin.css" rel="stylesheet"> 
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">Console</div>
        <div class="sidebar-menu">
            <a href="#" class="menu-item" class="menu-icon" alt="Home Icon"> Dashboard</a>
            <a href="adminpagina.php" class="menu-item" class="menu-icon" alt="User Icon"> Gebruikersbeheer</a>
            <a href="#" class="menu-item" class="menu-icon" alt="Storage Icon"> Opslagbeheer</a>
            <a href="tickets.php" class="menu-item" class="menu-icon" alt="Ticket Icon"> Tickets</a>
        </div>
    </div>
    <div class="main-content">
        <div class="ticket-details">
            <h2><?php echo htmlspecialchars($ticket['onderwerp']); ?></h2>
            <p><strong>Omschrijving:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($ticket['beschrijving'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
            <p><strong>Gemaakt op:</strong> <?php echo htmlspecialchars($ticket['gemaakt_op']); ?></p>
        
            <?php if ($user): ?>
                <p><strong>Gebruiker:</strong> <?php echo htmlspecialchars($user['naam']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
