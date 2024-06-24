<?php
// Подключение конфигурационного файла
include 'config.php';
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$amount = isset($_POST['amount']) ? $_POST['amount'] : 0;

if ($amount <= 0) {
    echo "Ошибка: Неверная сумма платежа.";
    echo '<br><a href="dashboard.php">Назад</a>';
    exit();
}

// Вставка новой записи оплаты с отметкой "Оплачено"
$stmt = $conn->prepare("INSERT INTO payments (user_id, amount, status) VALUES (?, ?, 'Оплачено')");
$stmt->bind_param("id", $user_id, $amount);
$stmt->execute();
$stmt->close();

// Пометка неоплаченных показаний как оплаченные
$stmt = $conn->prepare("UPDATE meter_readings SET is_paid = TRUE WHERE user_id = ? AND is_paid = FALSE");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php"); // Перенаправление обратно на страницу личного кабинета
exit();
?>
