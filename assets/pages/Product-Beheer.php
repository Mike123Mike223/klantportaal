<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/preferences.php");
    exit;
}

require_once '../dbinclude/db.php';

// Haal alle producten op met gekoppelde gebruikers
$query = $pdo->prepare("
    SELECT p.product_id, p.naam, p.type, p.registratie_datum, p.verloop_datum, p.domeinnaam, u.username, o.status
    FROM product p
    JOIN `order` o ON p.product_id = o.product_id
    JOIN user u ON o.user_id = u.user_id
");
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Controleer of er een actie is aangevraagd
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];

    if ($action === 'verlengen') {
        // Verleng de verloopdatum met bijvoorbeeld 1 jaar
        $stmt = $pdo->prepare("UPDATE product SET verloop_datum = DATE_ADD(verloop_datum, INTERVAL 1 YEAR) WHERE product_id = ?");
        $stmt->execute([$product_id]);
    } elseif ($action === 'opzeggen' || $action === 'verwijderen') {
        // Update de status naar 'Geannuleerd' of verwijder het product
        $stmt = $pdo->prepare("UPDATE `order` SET status = 'Geannuleerd' WHERE product_id = ?");
        $stmt->execute([$product_id]);
    }
    // Redirect om herladen van de pagina te voorkomen
    header('Location: Product-Beheer.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opslagbeheer</title>
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">Console</div>
        <div class="sidebar-menu">
            <a href="adminpagina.php" class="menu-item"> Gebruikersbeheer</a>
            <a href="Product-Beheer.php" class="menu-item"> Opslagbeheer</a>
            <a href="tickets.php" class="menu-item"> Tickets</a>
            <a href="manage_products.php" class="menu-item"> Aanvragen</a>
        </div>
        <form action="../includes/logout.php" method="post" class="logout-form">
            <button type="submit" class="logout-button">Uitloggen</button>
        </form>
    </div>
    <div class="main-content">
        <div class="create-product-section">
            <a href="Product-Aanvragen.php" class="create-product-button">Product toevoegen</a>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Naam</th>
                        <th>Type</th>
                        <th>Registratie Datum</th>
                        <th>Verloop Datum</th>
                        <th>Domeinnaam</th>
                        <th>Gebruiker</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_num = 1;
                    foreach ($products as $product) {
                        echo "<tr>
                            <td>{$row_num}</td>
                            <td>{$product['naam']}</td>
                            <td>{$product['type']}</td>
                            <td>{$product['registratie_datum']}</td>
                            <td>{$product['verloop_datum']}</td>
                            <td>{$product['domeinnaam']}</td>
                            <td>{$product['username']}</td>
                            <td>{$product['status']}</td>
                            <td>
                                <form action='Product-Beheer.php' method='post' style='display:inline;'>
                                    <input type='hidden' name='product_id' value='{$product['product_id']}'>
                                    <button type='submit' name='action' value='verlengen' class='btn btn-success btn-sm'>Verlengen</button>
                                    <button type='submit' name='action' value='opzeggen' class='btn btn-warning btn-sm'>Opzeggen</button>
                                    <button type='submit' name='action' value='verwijderen' class='btn btn-danger btn-sm'>Verwijderen</button>
                                </form>
                            </td>
                          </tr>";
                        $row_num++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
