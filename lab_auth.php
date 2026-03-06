<?php
// Сессии должны запускаться до любого вывода HTML
session_start();

// --- ЗАДАНИЕ №1: Работа с сессиями (Счетчик посещений) ---
if (!isset($_SESSION['visit_count'])) {
    $_SESSION['visit_count'] = 1; // Первое посещение
} else {
    $_SESSION['visit_count']++; // Увеличение при перезагрузке
}

// --- ЗАДАНИЕ №2: Работа с Cookies (Запись времени последнего визита) ---
$last_visit = "Впервые на сайте";
if (isset($_COOKIE['last_visit_time'])) {
    $last_visit = $_COOKIE['last_visit_time'];
}
// Устанавливаем куку на 1 час (текущее время + 3600 сек)
setcookie('last_visit_time', date("H:i:s d.m.Y"), time() + 3600, "/");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сессии и Cookies - Perfect Doors</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .info-box { background: #f4f7f9; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 5px solid var(--primary-blue); }
    </style>
</head>
<body>
    <header class="main-header"><div class="container"><h1>Работа с Session и Cookie</h1></div></header>
    
    <main class="container">
        <!-- Вывод данных сессии -->
        <div class="info-box">
            <h3>Задание №1: Сессии</h3>
            <p>Вы обновили эту страницу <strong><?php echo $_SESSION['visit_count']; ?></strong> раз(а) за текущий сеанс.</p>
            <p><small>(Данные удалятся, если вы закроете браузер)</small></p>
        </div>

        <!-- Вывод данных куки -->
        <div class="info-box">
            <h3>Задание №2: Cookies</h3>
            <p>Ваш предыдущий визит был зафиксирован: <strong><?php echo $last_visit; ?></strong></p>
            <p><small>(Данные сохранятся в браузере на 1 час даже после закрытия вкладки)</small></p>
        </div>
        
        <a href="login.php" class="btn">Перейти к авторизации (Задание №3)</a>
    </main>
</body>
</html>