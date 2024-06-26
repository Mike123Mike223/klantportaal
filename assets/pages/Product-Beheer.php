<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/preferences.php");
    exit;
}

require_once '../dbinclude/db.php';

$query = $pdo->prepare("SELECT product_id, naam, type, registratie_datum, verloop_datum, domeinnaam FROM product");
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);
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
