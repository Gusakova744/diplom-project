<?php
// Подключение конфигурационного файла
include 'config.php';

// Проверка, был ли запрос POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $account_number = $_POST['account_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверка, совпадают ли пароли
    if ($password !== $confirm_password) {
        echo "Пароли не совпадают.";
        exit();
    }

    // Проверка, что номер лицевого счета состоит ровно из 12 цифр
    if (!preg_match('/^\d{12}$/', $account_number)) {
        echo "Номер лицевого счета должен содержать ровно 12 цифр.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Хэширование пароля

    // Вставка нового пользователя в базу данных
    $stmt = $conn->prepare("INSERT INTO users (email, account_number, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $account_number, $hashed_password);
    $stmt->execute();
    $stmt->close();

    // Перенаправление на страницу логина после успешной регистрации
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>
    <h2>Регистрация</h2>
    <form method="post" action="registration.php">
        <label>Email:</label><input type="email" name="email" required>
        <label>Номер лицевого счета:</label><input type="text" name="account_number" required pattern="\d{12}" title="Номер лицевого счета должен содержать ровно 12 цифр">
        <label>Пароль:</label><input type="password" name="password" required>
        <label>Повторите пароль:</label><input type="password" name="confirm_password" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>
