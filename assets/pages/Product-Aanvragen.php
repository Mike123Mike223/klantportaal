<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

require_once '../dbinclude/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_naam = $_POST['product_naam']; // Aangepaste naam van het formulier veld

    // Verwijder de 'beschrijving' parameter aangezien deze niet nodig is.
    $query = $pdo->prepare("INSERT INTO product (naam, registratie_datum) VALUES (?, NOW())");
    $query->execute([$product_naam]);

    $success_message = "Uw productaanvraag is succesvol ingediend.";
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Aanvragen</title>
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
            <h1>Product Aanvragen</h1>
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <form action="Product-Aanvragen.php" method="post" class="create-user-form">
                <div class="form-group">
                    <label for="product_naam">Product Naam:</label>
                    <input type="text" id="product_naam" name="product_naam" required>
                </div>
                <button type="submit" class="submit-button">Aanvragen</button>
            </form>
        </div>
    </div>
</body>

</html>
