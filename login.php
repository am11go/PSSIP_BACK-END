<?php
session_start();

// Установим демонстрационный пароль
$admin_password = "admin123"; 
$error_message = "";

if (isset($_POST['do_login'])) {
    if ($_POST['pass'] === $admin_password) {
        // Если пароль верен, записываем флаг в сессию
        $_SESSION['is_admin'] = true;
        header("Location: admin.php"); // Перенаправляем в админку
        exit();
    } else {
        $error_message = "Неверный пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в панель управления - Perfect Doors</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .login-form { max-width: 400px; margin: 100px auto; padding: 30px; border: 1px solid #ddd; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Вход для администратора</h2>
            <?php if($error_message) echo "<p class='error'>$error_message</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Введите пароль (admin123):</label>
                    <input type="password" name="pass" style="width:100%; padding:10px; margin:10px 0;" required>
                </div>
                <button type="submit" name="do_login" class="btn" style="width:100%;">Войти</button>
            </form>
            <p><a href="lab_auth.php">← Назад к заданию 1-2</a></p>
        </div>
    </div>
</body>
</html>