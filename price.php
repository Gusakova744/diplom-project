<?php
// Подключение конфигурационного файла
include 'config.php';
session_start(); // Старт сессии

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php"); // Перенаправление на страницу логина, если не авторизован или не администратор
    exit();
}

// Проверка, был ли запрос POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $price_per_kw = $_POST['price_per_kw'];

    // Подготовка и выполнение SQL запроса для вставки новой цены
    $stmt = $conn->prepare("INSERT INTO prices (price_per_kw) VALUES (?)");
    $stmt->bind_param("d", $price_per_kw);
    $stmt->execute();
    $stmt->close();

    echo "Цена за 1 кВт/час успешно обновлена.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Установка цены за 1 кВт/час</title>
</head>
<body>
    <h2>Установка цены за 1 кВт/час</h2>
    <form method="post" action="price.php">
        <label>Цена за 1 кВт/час:</label><input type="number" step="0.01" name="price_per_kw" required>
        <button type="submit">Установить</button>
    </form>
</body>
</html>
