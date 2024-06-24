<?php
session_start();
require_once '../dbinclude/db.php';
require_once '../templates/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $onderwerp = $_POST['onderwerp'];
    $beschrijving = $_POST['beschrijving'];
  
    
    $insert_query = $pdo->prepare("
        INSERT INTO ticket (user_id, onderwerp, beschrijving, gemaakt_op)
        VALUES (?, ?, ?, NOW())
    ");
    if ($insert_query->execute([$_SESSION['user_id'], $onderwerp, $beschrijving])) {
        $_SESSION['success_message'] = 'Ticket succesvol aangevraagd.';
        header('Location: mijntickets.php'); 
        exit;
    } else {
        $_SESSION['error_message'] = 'Er is een fout opgetreden bij het aanvragen van het ticket.';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Aanvragen</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Ticket Aanvragen</h1>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <form action="ticket_aanvragen.php" method="post">
            <div class="form-group">
                <label for="onderwerp">Onderwerp</label>
                <input type="text" class="form-control" id="onderwerp" name="onderwerp" required>
            </div>
            <div class="form-group">
                <label for="beschrijving">Beschrijving</label>
                <textarea class="form-control" id="beschrijving" name="beschrijving" rows="4" required></textarea>
            </div>
         
            </div> 
            <button type="submit" class="btn btn-primary">Aanvragen</button>
        </form>
    </div>

    <?php require_once '../templates/footer.php'; ?>
</body>
</html>
