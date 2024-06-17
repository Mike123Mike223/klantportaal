<?php
session_start();
require_once '../dbinclude/db.php';
require_once '../templates/header.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: /login.php');
    exit;
}

// Haal de gebruikersgegevens op
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT naam, bedrijfsnaam, adres, email FROM user WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verwerk het formulier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];
    $bedrijfsnaam = $_POST['bedrijfsnaam'];
    $adres = $_POST['adres'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare('UPDATE user SET naam = ?, bedrijfsnaam = ?, adres = ?, email = ? WHERE user_id = ?');
    $stmt->execute([$naam, $bedrijfsnaam, $adres, $email, $user_id]);

    // Zet een succesbericht in de sessie
    $_SESSION['success_message'] = "Profiel succesvol bijgewerkt.";
    header('Location: profile.php');
    exit;
}
?>

<h1>Profiel Wijzigen</h1>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']); // Verwijder het bericht na weergave
}
?>

<form action="profile.php" method="post">
    <div class="mb-3">
        <label for="naam" class="form-label">Naam</label>
        <input type="text" class="form-control" id="naam" name="naam" value="<?= htmlspecialchars($user['naam']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="bedrijfsnaam" class="form-label">Bedrijfsnaam</label>
        <input type="text" class="form-control" id="bedrijfsnaam" name="bedrijfsnaam" value="<?= htmlspecialchars($user['bedrijfsnaam']) ?>">
    </div>
    <div class="mb-3">
        <label for="adres" class="form-label">Adres</label>
        <input type="text" class="form-control" id="adres" name="adres" value="<?= htmlspecialchars($user['adres']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Opslaan</button>
</form>

<?php
require_once '../templates/footer.php';
?>
