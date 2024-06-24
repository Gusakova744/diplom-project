<?php
// Подключение конфигурационного файла
include 'config.php';
session_start(); // Старт сессии

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reading = intval($_POST['reading']);

// Получение последнего переданного показания
$stmt = $conn->prepare("SELECT reading FROM meter_readings WHERE user_id = ? ORDER BY date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($last_reading);
$stmt->fetch();
$stmt->close();

// Проверка нового показания
if ($reading < 0) {
    echo "Показания не могут быть отрицательными.";
    echo '<br><a href="dashboard.php">Назад</a>';
} elseif ($reading <= $last_reading) {
    echo "Данные не верные. Новые показания должны быть больше предыдущих. Если у вас новый счетчик, обратитесь в тех. поддержку.";
    echo '<br><a href="dashboard.php">Назад</a>';
} else {
    // Вставка нового показания
    $stmt = $conn->prepare("INSERT INTO meter_readings (user_id, reading) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $reading);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
}
?>
