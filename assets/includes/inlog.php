<?php
session_start();
require_once '../dbinclude/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT user_id as id, naam, wachtwoord, is_admin FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($password == $user['wachtwoord']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['is_admin'] ? 'admin' : 'user';

                if ($user['is_admin']) {
                    header("Location: ../pages/adminpagina.php");
                } else {
                    header("Location: ../pages/products.php");
                }
                exit;
            } else {
                $_SESSION['error_message'] = "Incorrecte email of wachtwoord.";
                header("Location: ../pages/index.php");
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Geen gebruiker gevonden met het opgegeven e-mailadres.";
            header("Location: ../pages/index.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
