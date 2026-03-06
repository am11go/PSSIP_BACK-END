<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа PHP - Perfect Doors</title>
    <!-- Подключаем ваши стили -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Дополнительные стили специально для лабораторной работы */
        .lab-container { padding: 50px 20px; max-width: 1200px; margin: 0 auto; min-height: 50vh; }
        .lab-menu { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; }
        .lab-menu a { display: inline-block; padding: 10px 20px; background-color: var(--light-gray-bg); border: 1px solid var(--primary-blue); border-radius: 5px; color: var(--dark-text); font-weight: 500;}
        .lab-menu a:hover, .lab-menu a.active { background-color: var(--primary-blue); color: var(--white); }
        .lab-content { background: var(--light-gray-bg); padding: 30px; border-radius: 8px; border-left: 5px solid var(--accent-blue); }
        .code-block { background: #fff; padding: 15px; border: 1px solid #ddd; margin-top: 15px; font-family: monospace; }
    </style>
</head>
<body>

    <!-- Шапка сайта (оставлена для сохранения дизайна) -->
    <header>
        <div class="main-header">
            <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
                <a href="index.html" class="logo">
                    <img src="assets/images/logo.png" alt="Perfect Doors Logo" style="height: 45px;">
                </a>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.html">На главную</a></li>
                        <li><a href="lab.php" class="text-accent-blue">Лабораторная работа PHP</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="lab-container">
        <h2>Практическая часть: Основы PHP</h2>

        <!-- ПУНКТ 1: Страница со ссылками на php-скрипты -->
        <ul class="lab-menu">
            <li><a href="?task=info" target="_blank">Пункт 2: Настройки PHP (phpinfo)</a></li>
            <li><a href="?task=hello">Пункт 3: Приветствие</a></li>
            <li><a href="?task=vars">Пункт 4: Переменные стиля</a></li>
            <li><a href="?task=types">Пункт 5: Типы данных и Константы</a></li>
            <li><a href="?task=predefined">Пункт 6: Предопределенные переменные</a></li>
        </ul>

        <div class="lab-content">
            <?php
            // Получаем номер задания из URL (маршрутизация)
            $task = isset($_GET['task']) ? $_GET['task'] : '';

            // Выполняем скрипт в зависимости от выбранного пункта меню
            switch ($task) {
                
                // ПУНКТ 2: Получите информацию о настройках php с помощью команды phpinfo()
                case 'info':
                    // phpinfo() выводит свою огромную системную HTML страницу, поэтому мы открываем ее в новой вкладке 
                    // (см. target="_blank" в ссылке меню выше), чтобы не ломать верстку сайта
                    phpinfo();
                    exit; // Останавливаем дальнейший вывод дизайна

                // ПУНКТ 3: Скрипт с текстом "Привет всем!!!" и инфо о разработчике
                case 'hello':
                    echo "<h3>Задание 3: Простейший скрипт</h3>";
                    
                    // Выводим строку с помощью echo
                    echo "<h1>Привет всем!!!</h1>";
                    
                    // Выводим информацию о разработчике
                    echo "<p><strong>Разработчик скрипта:</strong> Студент группы ПЗТ-41, Борисов Илья</p>";
                    break;

                // ПУНКТ 4: Использование переменных $color и $size
                case 'vars':
                    echo "<h3>Задание 4: Переменные \$color и \$size</h3>";
                    
                    // Инициализация переменных (используем цвета из вашей CSS-палитры)
                    $color = "#4154D9"; // Акцентный синий цвет
                    $size = "32px";     // Размер шрифта
                    $developer_name = "Иванов Иван Иванович"; // Замените на свое ФИО
                    
                    // Формируем HTML строку, встраивая значения переменных прямо в атрибут style
                    echo "<p style='color: {$color}; font-size: {$size}; font-weight: bold;'>
                            Разработчик: {$developer_name}
                          </p>";
                    break;

                // ПУНКТ 5: Константа e и изменение типов переменной
                case 'types':
                    echo "<h3>Задание 5: Константа e и изменение типов</h3>";
                    
                    // Создаем константу NUM_E со значением 2.71828
                    define("NUM_E", 2.71828);
                    
                    // Выводим значение константы на экран
                    echo "<p>Число e равно <b>" . NUM_E . "</b></p>";
                    
                    echo "<div class='code-block'>";
                    
                    // Присваиваем переменной $num_e1 значение константы
                    $num_e1 = NUM_E;
                    echo "Исходная переменная \$num_e1: значение = {$num_e1}, тип = <b>" . gettype($num_e1) . "</b><br><br>";
                    
                    // Изменяем тип на строковый (string)
                    $num_e1 = (string)$num_e1;
                    echo "После приведения к строковому типу: значение = '{$num_e1}', тип = <b>" . gettype($num_e1) . "</b><br><br>";
                    
                    // Изменяем тип на целое число (int)
                    $num_e1 = (int)$num_e1;
                    echo "После приведения к целому типу: значение = {$num_e1}, тип = <b>" . gettype($num_e1) . "</b><br><br>";
                    
                    // Изменяем тип на булевский (bool)
                    $num_e1 = (bool)$num_e1;
                    // Для корректного отображения true/false на экране используем тернарный оператор
                    $bool_text = $num_e1 ? 'true' : 'false';
                    echo "После приведения к булевскому типу: значение = {$bool_text}, тип = <b>" . gettype($num_e1) . "</b><br>";
                    
                    echo "</div>";
                    break;

                // ПУНКТ 6: Предопределенные константы и переменные
                case 'predefined':
                    echo "<h3>Задание 6: Предопределенные константы и переменные</h3>";
                    
                    echo "<div class='code-block'>";
                    echo "<h4>Предопределенные константы PHP:</h4>";
                    
                    // Вывод версии установленного PHP
                    echo "Версия PHP (PHP_VERSION): <b>" . PHP_VERSION . "</b><br>";
                    // Вывод операционной системы сервера (в XAMPP это будет WINNT)
                    echo "Операционная система (PHP_OS): <b>" . PHP_OS . "</b><br>";
                    // Вывод полного пути к текущему исполняемому файлу
                    echo "Текущий файл (__FILE__): <b>" . __FILE__ . "</b><br>";
                    // Вывод текущей строки программного кода
                    echo "Текущая строка кода (__LINE__): <b>" . __LINE__ . "</b><br>";
                    
                    echo "<h4>Предопределенные (суперглобальные) переменные массива \$_SERVER:</h4>";
                    
                    // Вывод имени сервера (localhost)
                    echo "Имя сервера (\$_SERVER['SERVER_NAME']): <b>" . $_SERVER['SERVER_NAME'] . "</b><br>";
                    // Вывод данных о браузере, с которого зашел пользователь
                    echo "Ваш браузер (\$_SERVER['HTTP_USER_AGENT']): <b>" . $_SERVER['HTTP_USER_AGENT'] . "</b><br>";
                    // Вывод IP адреса клиента (в XAMPP обычно ::1 или 127.0.0.1)
                    echo "Ваш IP адрес (\$_SERVER['REMOTE_ADDR']): <b>" . $_SERVER['REMOTE_ADDR'] . "</b><br>";
                    echo "</div>";
                    break;

                // Вывод по умолчанию (когда страница только загрузилась и скрипт еще не выбран)
                default:
                    echo "<h3>Добро пожаловать!</h3>";
                    echo "<p>Выберите один из пунктов меню выше для просмотра результатов выполнения скриптов.</p>";
                    break;
            }
            ?>
        </div>
    </main>

    <!-- Подвал сайта (оставлен для сохранения дизайна) -->
    <footer>
        <div class="footer-bottom" style="margin-top: 50px;">
            <div class="container">
                <p>© 2024 - Perfect Doors (Лабораторная работа)</p>
            </div>
        </div>
    </footer>

</body>
</html>