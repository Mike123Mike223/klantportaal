<?php
session_start();
require_once '../dbinclude/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

// Haal alle producten met status 'In Behandeling' op
$stmt = $pdo->prepare('SELECT o.order_id, p.product_id, p.naam, p.type, o.status, p.registratie_datum, p.verloop_datum, p.domeinnaam FROM `order` o JOIN product p ON o.product_id = p.product_id WHERE o.status = ?');
$stmt->execute(['In Behandeling']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Controleer of er een status update is aangevraagd
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare('UPDATE `order` SET status = ? WHERE order_id = ?');
    $stmt->execute([$status, $order_id]);

    // Redirect om herladen van de pagina te voorkomen
    header('Location: manage_products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Producten</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Beheer Producten</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Registratie Datum</th>
                    <th>Verloop Datum</th>
                    <th>Domeinnaam</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['naam']) ?></td>
                        <td><?= htmlspecialchars($product['type']) ?></td>
                        <td><?= htmlspecialchars($product['status']) ?></td>
                        <td><?= htmlspecialchars($product['registratie_datum']) ?></td>
                        <td><?= htmlspecialchars($product['verloop_datum']) ?></td>
                        <td><?= htmlspecialchars($product['domeinnaam']) ?></td>
                        <td>
                            <form action="manage_products.php" method="post" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $product['order_id'] ?>">
                                <select name="status" class="form-control" style="display:inline-block; width:auto;">
                                    <option value="In Behandeling" <?= $product['status'] == 'In Behandeling' ? 'selected' : '' ?>>In Behandeling</option>
                                    <option value="Actief" <?= $product['status'] == 'Actief' ? 'selected' : '' ?>>Actief</option>
                                    <option value="Geannuleerd" <?= $product['status'] == 'Geannuleerd' ? 'selected' : '' ?>>Geannuleerd</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
