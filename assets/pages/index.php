<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klantportaal</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<div class="main-container">
    <div class="header">
        <div class="logo-circle"></div>
        <div class="logo-text">Logo</div>
    </div>
    <div class="login-container">
        <h2 class="login-title">Log in</h2>
        <form class="login-form" action="../includes/inlog.php" method="post">
            <div class="input-container">
                <img
                    loading="lazy"
                    src="https://cdn.builder.io/api/v1/image/assets/TEMP/a43dd05212fb833c3b8d424883fd708487c95c35ef636a824dc0c02d7a3db4a1?apiKey=f3d39465ea124a7d89e25ca599634180&"
                    class="input-icon"
                    alt="Email Icon"
                />
                <input type="email" name="email" placeholder="Your email" class="input-field" required>
            </div>
            <div class="input-container">
                <img
                    loading="lazy"
                    src="https://cdn.builder.io/api/v1/image/assets/TEMP/e423ff2bf33ce92fe5de4a35577f42e1ecec2b0e2d2d85454b519a102fe67c78?apiKey=f3d39465ea124a7d89e25ca599634180&"
                    class="input-icon"
                    alt="Password Icon"
                />
                <input type="password" name="password" placeholder="Password" class="input-field" required>
            </div>
            <button type="submit" class="login-button">Log In</button>
        </form>
        <a href="forgot_password.php" class="forgot-password-link">Forgot password?</a>
    </div>
</div>
</body>
</html>
