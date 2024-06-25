<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminpagina.php");
    exit;
}

require_once '../dbinclude/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = htmlspecialchars($_POST['naam']);
    $email = htmlspecialchars($_POST['email']);
    $bedrijfsnaam = htmlspecialchars($_POST['bedrijfsnaam']);
    $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($naam)) {
        $errors[] = "Naam is verplicht";
    }
    if (empty($email)) {
        $errors[] = "E-mail is verplicht";
    }
    if (empty($wachtwoord)) {
        $errors[] = "Wachtwoord is verplicht";
    }

    if (empty($errors)) {
        try {
            $query = $pdo->prepare("INSERT INTO `user` (naam, email, bedrijfsnaam, wachtwoord, is_admin) VALUES (:naam, :email, :bedrijfsnaam, :wachtwoord, :is_admin)");
            $query->execute([
                ':naam' => $naam,
                ':email' => $email,
                ':bedrijfsnaam' => $bedrijfsnaam,
                ':wachtwoord' => $wachtwoord,
                ':is_admin' => $is_admin
            ]);

            if ($query->rowCount() > 0) {
                $_SESSION['success_message'] = "Gebruiker $naam succesvol toegevoegd.";
                header("Location: adminpagina.php");
                exit;
            } else {
                $errors[] = "Er is een probleem opgetreden bij het toevoegen van de gebruiker. Probeer het opnieuw.";
            }
        } catch (PDOException $e) {
            $errors[] = "Databasefout: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Aanmaken</title>
    <link href="../css/admin.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">Console</div>
            <div class="sidebar-menu">
                <a href="adminpagina.php" class="menu-item">Gebruikersbeheer</a>
                <a href="#" class="menu-item">Opslagbeheer</a>
                <a href="tickets.php" class="menu-item">Tickets</a>
            </div>
            <form action="../includes/logout.php" method="post" class="logout-form">
                <button type="submit" class="logout-button">Uitloggen</button>
            </form>
        </div>
        <div class="main-content">
            <h2>Gebruiker Aanmaken</h2>

       
            <form action="User-Aanmaken.php" method="post" class="create-user-form">
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="bedrijfsnaam">Bedrijfsnaam:</label>
                <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" required>

                <label for="wachtwoord">Wachtwoord:</label>
                <input type="password" id="wachtwoord" name="wachtwoord" required>

                <label for="is_admin">Admin:</label>
                <input type="checkbox" id="is_admin" name="is_admin">

                <button type="submit" class="create-user-button">Gebruiker Aanmaken</button>
            </form>
        </div>
    </div>
</body>

</html>
