<?php
// Подключение конфигурационного файла
include 'config.php';
session_start(); // Старт сессии

// Проверка, был ли запрос POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Поиск пользователя по email
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Проверка пароля
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id; // Сохранение ID пользователя в сессии
        header("Location: dashboard.php"); // Перенаправление на страницу личного кабинета
    } else {
        echo "Неверные учетные данные.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
</head>
<body>
    <h2>Вход</h2>
    <form method="post" action="login.php">
        <label>Email:</label><input type="email" name="email" required>
        <label>Пароль:</label><input type="password" name="password" required>
        <button type="submit">Войти</button>
    </form>
    <p>Нет учетной записи? <a href="registration.php">Зарегистрируйтесь здесь</a></p>
    <p><a href="forgot_password.php">Забыли пароль?</a></p>
</body>
</html>
