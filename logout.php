<?php
session_start();
// Полностью очищаем сессию
session_unset();
session_destroy();
// Возвращаем на страницу входа
header("Location: login.php");
exit();