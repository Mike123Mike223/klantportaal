<?php
require_once '../dbinclude/db.php';
require_once '../templates/header.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Haal de producten van de gebruiker op
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT p.naam, p.type, o.status, p.registratie_datum, p.verloop_datum, p.domeinnaam FROM `order` o JOIN product p ON o.product_id = p.product_id WHERE o.user_id = ?');
$stmt->execute([$user_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Mijn Producten</h1>
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
                    <?php if ($product['status'] == 'Actief'): ?>
                        <form action="/pages/cancel_product.php" method="post">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Opzeggen</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once '../templates/footer.php';
?>
