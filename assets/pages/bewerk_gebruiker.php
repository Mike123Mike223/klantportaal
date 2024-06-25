<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/preferences.php");
    exit;
}

require_once '../dbinclude/db.php';

$errors = [];
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$user_id) {
    header("Location: adminpagina.php");
    exit;
}


$query = $pdo->prepare("SELECT * FROM user WHERE user_id = :user_id");
$query->execute([':user_id' => $user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = "Gebruiker niet gevonden.";
    header("Location: adminpagina.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = htmlspecialchars($_POST['naam']);
    $email = htmlspecialchars($_POST['email']);
    $bedrijfsnaam = htmlspecialchars($_POST['bedrijfsnaam']);
    $wachtwoord = !empty($_POST['wachtwoord']) ? password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT) : $user['wachtwoord'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($naam)) {
        $errors[] = "Naam is verplicht";
    }
    if (empty($email)) {
        $errors[] = "E-mail is verplicht";
    }

    if (empty($errors)) {
        try {
            $query = $pdo->prepare("UPDATE `user` SET naam = :naam, email = :email, bedrijfsnaam = :bedrijfsnaam, wachtwoord = :wachtwoord, is_admin = :is_admin WHERE user_id = :user_id");
            $query->execute([':naam' => $naam,
                            ':email' => $email,
                            ':bedrijfsnaam' => $bedrijfsnaam,
                            ':wachtwoord' => $wachtwoord,
                            ':is_admin' => $is_admin,
                            ':user_id' => $user_id
            ]);

            if ($query->rowCount() > 0) {
                $_SESSION['success_message'] = "Gebruiker {$user['naam']} succesvol bijgewerkt.";
                header("Location: adminpagina.php");
                exit;
            } else {
                $errors[] = "Er is een probleem opgetreden bij het bijwerken van de gebruiker. Probeer het opnieuw.";
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
    <title>Gebruiker Bewerken</title>
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
            <h2>Gebruiker Bewerken</h2>

            <?php if (!empty($errors)) : ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="bewerk_gebruiker.php?id=<?php echo $user['user_id']; ?>" method="post" class="edit-user-form">
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($user['naam']); ?>" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="bedrijfsnaam">Bedrijfsnaam:</label>
                <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" value="<?php echo htmlspecialchars($user['bedrijfsnaam']); ?>" required>

                <label for="wachtwoord">Nieuw Wachtwoord:</label>
                <input type="password" id="wachtwoord" name="wachtwoord">

                <label for="is_admin">Admin:</label>
                <input type="checkbox" id="is_admin" name="is_admin" <?php if ($user['is_admin'] == 1) echo 'checked'; ?>>

                <button type="submit" class="edit-user-button">Gebruiker Bijwerken</button>
            </form>
        </div>
    </div>
</body>

</html>
