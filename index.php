<?php
// Старт сессии
session_start();

// Проверка, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    // Если пользователь авторизован, перенаправляем на страницу личного кабинета
    header("Location: dashboard.php");
    exit();
} else {
    // Если пользователь не авторизован, перенаправляем на страницу логина
    header("Location: login.php");
    exit();
}
?>