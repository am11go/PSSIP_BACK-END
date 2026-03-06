<?php
/**
 * Лабораторная работа
 * Задания №1 и №2: Работа с файловой системой и датами в PHP
 */

// --- ЗАДАНИЕ №2: Специальная функция для дня недели на русском ---
function getRussianWeekday($date_str) {
    // Получаем сокращенное английское название дня недели
    $weekday = date('D', strtotime($date_str));
    
    // Переопределение на русский язык согласно условию
    if ($weekday == 'Mon') { $weekday = "понедельник"; }
    if ($weekday == 'Tue') { $weekday = "вторник"; }
    if ($weekday == 'Wed') { $weekday = "среда"; }
    if ($weekday == 'Thu') { $weekday = "четверг"; }
    if ($weekday == 'Fri') { $weekday = "пятница"; }
    if ($weekday == 'Sat') { $weekday = "суббота"; }
    if ($weekday == 'Sun') { $weekday = "воскресенье"; }
    
    return $weekday;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа - Perfect Doors</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Дополнительные стили для оформления вывода PHP */
        .lab-section { padding: 40px 0; border-bottom: 1px solid #eee; }
        .php-output { 
            background: #f9f9f9; 
            padding: 20px; 
            border-left: 5px solid var(--primary-blue); 
            margin-top: 15px;
            font-family: 'Courier New', monospace;
        }
        .task-title { color: var(--accent-blue); margin-bottom: 10px; }
    </style>
</head>
<body>

<header class="main-header">
    <div class="container">
        <a href="index.html" class="logo">
            <img src="assets/images/logo.png" alt="Logo">
        </a>
        <h1 style="font-size: 1.5em;">Отчет по лабораторной работе</h1>
    </div>
</header>

<main class="container">

    <!-- ЗАДАНИЕ №1: Работа с файловой системой -->
    <section class="lab-section">
        <h2>Задание №1: Файловая система</h2>

        <!-- ПРИМЕР 1 -->
        <div class="task-title">Пример 1: Свойства файла (lab2.php)</div>
        <div class="php-output">
            <?php
            $filename = "lab2.php";
            if (file_exists($filename)) {
                echo "<h1>Файл: $filename</h1>";
                echo "<p>В последний раз редактировался: " . date("r", filemtime($filename)) . "</p>";
                echo "<p>В последний раз был открыт: " . date("r", fileatime($filename)) . "</p>";
                echo "<p>Размер: " . filesize($filename) . " байт</p>";
            } else {
                echo "Файл не найден.";
            }
            ?>
        </div>

        <!-- ПРИМЕР 2 -->
        <div class="task-title" style="margin-top:20px;">Пример 2: Работа с папками (assets/images/)</div>
        <div class="php-output">
            <?php
            $dir = "assets/images/";
            if (is_dir($dir)) {
                if ($folder = opendir($dir)) {
                    echo "<strong>Список файлов в папке:</strong><br>";
                    while (($entry = readdir($folder)) !== false) {
                        // Скрываем служебные ссылки . и ..
                        if ($entry != "." && $entry != "..") {
                            echo $entry . "<br />";
                        }
                    }
                    closedir($folder);
                }
            } else {
                echo "Папка не найдена.";
            }
            ?>
        </div>

        <!-- ПРИМЕР 3 -->
        <div class="task-title" style="margin-top:20px;">Пример 3: Чтение из файла (1.txt)</div>
        <div class="php-output">
            <?php
            // Создадим файл для примера, если его нет
            if(!file_exists("1.txt")) file_put_contents("1.txt", "Первая строка файла.\nВторая строка файла.");
            
            $f = fopen("1.txt", "r");
            echo "<strong>Содержимое файла построчно:</strong><br>";
            while(!feof($f)) {
                echo fgets($f) . "<br />";
            }
            fclose($f);
            ?>
        </div>

        <!-- ПРИМЕР 4 -->
        <div class="task-title" style="margin-top:20px;">Пример 4: Запись в файл (textfile.txt)</div>
        <div class="php-output">
            <?php
            // Запись в файл
            $f = fopen("textfile.txt", "w");
            fwrite($f, "PHP - мощный инструмент для разработки сайтов дверей!");
            fclose($f);

            // Чтение записанного
            $f = fopen("textfile.txt", "r");
            echo "<strong>Записано и прочитано:</strong> " . fgets($f);
            fclose($f);
            ?>
        </div>
    </section>

    <!-- ЗАДАНИЕ №2: Дата и время -->
    <section class="lab-section">
        <h2>Задание №2: Дата, время и календарь</h2>
        
        <div class="task-title">Текущая дата и время (3 строки):</div>
        <div class="php-output">
            <?php
            // Вывод в три строки
            echo date("d. m. Y") . "<br>"; // Краткий формат
            echo date("H:i:s") . "<br>";    // Время
            echo getRussianWeekday(date("Y-m-d")) . "<br>"; // День недели через функцию
            ?>
        </div>

        <div class="task-title" style="margin-top:20px;">Интерактивная форма:</div>
        <div class="php-output">
            <p>Сегодняшняя дата: <strong><?php echo date("d.m.Y"); ?></strong></p>
            
            <form method="POST">
                <button type="submit" name="show_day" class="btn btn-small">Узнать день недели</button>
            </form>

            <?php
            // Обработка нажатия кнопки
            if (isset($_POST['show_day'])) {
                $today_ru = getRussianWeekday(date("Y-m-d"));
                echo "<p style='margin-top:15px; color:var(--primary-blue); font-weight:bold;'>";
                echo "Сегодня день недели: " . $today_ru;
                echo "</p>";
            }
            ?>
        </div>
    </section>

</main>

<footer class="footer-bottom">
    <div class="container">
        <p>© <?php echo date("Y"); ?> - Лабораторная работа выполнена успешно</p>
    </div>
</footer>

</body>
</html>