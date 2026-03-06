<?php
/**
 * Практическая работа: Отправка данных на Email
 */

// 1. Настройки (куда отправлять)
$to_email = "ilya.borisov.grodno@gmail.com"; // ЗАМЕНИТЕ на ваш реальный email для теста
$subject = "Новая заявка на замер дверей: Perfect Doors";

// 2. Проверка, что форма была отправлена методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Получение и очистка данных (защита от инъекций)
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
    $date = htmlspecialchars(strip_tags(trim($_POST['date'])));

    // Простая проверка на заполненность
    if (empty($name) || empty($phone)) {
        echo "<div style='color:red;'>Ошибка: Заполните обязательные поля (Имя и Телефон)!</div>";
        exit;
    }

    // 4. Формирование текста письма (HTML-разметка)
    $message = "
    <html>
    <head>
        <title>Заявка с сайта Perfect Doors</title>
    </head>
    <body>
        <h2>Данные клиента:</h2>
        <p><strong>Имя:</strong> $name</p>
        <p><strong>Телефон:</strong> $phone</p>
        <p><strong>Желаемая дата замера:</strong> " . ($date ? $date : "Не указана") . "</p>
        <hr>
        <p>Письмо отправлено автоматически с вашего сайта.</p>
    </body>
    </html>
    ";

    // 5. Заголовки письма (важно для корректного отображения кириллицы и HTML)
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: info@perfect-doors.ru" . "\r\n"; // От кого (адрес вашего домена)
    $headers .= "Reply-To: $to_email" . "\r\n";

    // 6. Отправка письма
    if (mail($to_email, $subject, $message, $headers)) {
        // Успех
        echo "<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2 style='color:green;'>Заявка успешно отправлена!</h2>
                <p>Мы свяжемся с вами в ближайшее время.</p>
                <a href='index.html' style='color:#479DCB;'>Вернуться на главную</a>
              </div>";
    } else {
        // Ошибка сервера
        echo "<div style='color:red;'>Ошибка: Не удалось отправить письмо. Проверьте настройки почтового сервера.</div>";
    }
} else {
    // Если файл открыт напрямую
    header("Location: index.html");
}
?>