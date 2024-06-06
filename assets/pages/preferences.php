<?php
require_once '../dbinclude/db.php';
require_once '../templates/header.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Haal de betaalvoorkeuren op
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT type FROM paymentpreference WHERE user_id = ?');
$stmt->execute([$user_id]);
$preference = $stmt->fetch(PDO::FETCH_ASSOC);

// Haal het mandaat op indien van toepassing
$mandate = null;
if ($preference && $preference['type'] == 'Automatisch Incasso') {
    $stmt = $pdo->prepare('SELECT iban, date, account_naam, handtekening FROM mandate WHERE preference_id = ?');
    $stmt->execute([$preference['preference_id']]);
    $mandate = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verwerk het formulier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];

    // Verwijder bestaande voorkeur en mandaat als er een nieuwe voorkeur wordt gekozen
    $stmt = $pdo->prepare('DELETE FROM paymentpreference WHERE user_id = ?');
    $stmt->execute([$user_id]);

    if ($type == 'Automatisch Incasso') {
        $iban = $_POST['iban'];
        $date = $_POST['date'];
        $account_naam = $_POST['account_naam'];
        $handtekening = $_POST['handtekening'];

        // Voeg de betaalvoorkeur en het mandaat toe
        $stmt = $pdo->prepare('INSERT INTO paymentpreference (user_id, type) VALUES (?, ?)');
        $stmt->execute([$user_id, $type]);
        $preference_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO mandate (preference_id, iban, date, account_naam, handtekening) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$preference_id, $iban, $date, $account_naam, $handtekening]);
    } else {
        // Voeg de betaalvoorkeur toe
        $stmt = $pdo->prepare('INSERT INTO paymentpreference (user_id, type) VALUES (?, ?)');
        $stmt->execute([$user_id, $type]);
    }

    header('Location: /pages/preferences.php');
    exit;
}
?>

<h1>Betaalvoorkeuren</h1>
<form action="preferences.php" method="post">
    <div class="mb-3">
        <label for="type" class="form-label">Betaalvoorkeur</label>
        <select class="form-select" id="type" name="type" required>
            <option value="Factuur" <?= $preference && $preference['type'] == 'Factuur' ? 'selected' : '' ?>>Factuur</option>
            <option value="Automatisch Incasso" <?= $preference && $preference['type'] == 'Automatisch Incasso' ? 'selected' : '' ?>>Automatisch Incasso</option>
        </select>
    </div>
    <div id="mandateFields" style="display: <?= $preference && $preference['type'] == 'Automatisch Incasso' ? 'block' : 'none' ?>;">
        <div class="mb-3">
            <label for="iban" class="form-label">IBAN</label>
            <input type="text" class="form-control" id="iban" name="iban" value="<?= htmlspecialchars($mandate['iban'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Datum</label>
            <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($mandate['date'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="account_naam" class="form-label">Naam rekeninghouder</label>
            <input type="text" class="form-control" id="account_naam" name="account_naam" value="<?= htmlspecialchars($mandate['account_naam'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="handtekening" class="form-label">Handtekening</label>
            <input type="text" class="form-control" id="handtekening" name="handtekening" value="<?= htmlspecialchars($mandate['handtekening'] ?? '') ?>">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Opslaan</button>
</form>

<script>
document.getElementById('type').addEventListener('change', function () {
    var mandateFields = document.getElementById('mandateFields');
    if (this.value === 'Automatisch Incasso') {
        mandateFields.style.display = 'block';
    } else {
        mandateFields.style.display = 'none';
    }
});
</script>

<?php
require_once '../templates/footer.php';
?>
