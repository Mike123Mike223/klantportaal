<?php
session_start();
require_once '../dbinclude/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // admin 
        $stmt = $pdo->prepare("SELECT admin_id as id, naam, wachtwoord, 'admin' as role FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // gebruiker
            $stmt = $pdo->prepare("SELECT user_id as id, naam, wachtwoord, 'user' as role FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
        }

        if ($user) {



            if ($password == $user['wachtwoord']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'admin') {
                    header("Location: ../pages/adminpagina.php");
                } else {
                    header("Location: ../pages/gebruikerpagina.php");
                }
                exit;
            } else {

                echo "Password verification failed.";
                $_SESSION['error_message'] = "Incorrecte email of wachtwoord.";
                header("Location: ../pages/index.php");
                exit;
            }
        } else {

            echo "No user found with the given email.";
            $_SESSION['error_message'] = "Incorrecte email of wachtwoord.";
            header("Location: ../pages/index.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
