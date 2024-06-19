<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/preferences.php");
    exit;
}

require_once '../dbinclude/db.php';

$query = $pdo->prepare("SELECT user_id, naam, email, bedrijfsnaam FROM user WHERE is_admin = 1");
$query->execute();
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
<div class="sidebar">
            <div class="sidebar-header">Console</div>
            <div class="sidebar-menu">
                <a href="#" class="menu-item"> Dashboard</a>
                <a href="adminpagina.php" class="menu-item"> Gebruikersbeheer</a>
                <a href="#" class="menu-item"> Opslagbeheer</a>
                <a href="tickets.php" class="menu-item"> Tickets</a>
            </div>
            <form action="../includes/logout.php" method="post" class="logout-form">
                <button type="submit" class="logout-button">Uitloggen</button>
            </form>
        </div>
    <div class="main-content">
        <div class="create-user-section">
            <button class="create-user-button">Gebruikers aanmaken</button>
        </div>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Naam</th>
                        <th>E-mail</th>
                        <th>Bedrijfsnaam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_num = 1;
                    foreach ($users as $user) {
                        echo "<tr>
                                <td>{$row_num}</td>
                                <td>{$user['naam']}</td>
                                <td>{$user['email']}</td>
                                <td>{$user['bedrijfsnaam']}</td>
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
