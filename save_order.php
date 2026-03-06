<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $date = $_POST['date']; // Дата замера из календаря

    $sql = "INSERT INTO orders (name, phone, order_date, status) VALUES (?, ?, ?, 'new')";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$name, $phone, $date])) {
        echo "Заказ успешно сохранен в базу данных под номером " . $pdo->lastInsertId();
    } else {
        echo "Ошибка сохранения заказа.";
    }
}
?>
