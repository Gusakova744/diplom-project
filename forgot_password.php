<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Проверка, существует ли email в базе данных
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Генерация уникального токена
        $token = bin2hex(random_bytes(16));

        // Вставка токена в таблицу password_resets
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token)");
        $stmt->bind_param("is", $user_id, $token);
        $stmt->execute();
        $stmt->close();

        // Отправка email пользователю с ссылкой для восстановления пароля
        $reset_link = "http://localhost/reset_password.php?token=$token";
        $subject = "Восстановление пароля";
        $message = "Перейдите по следующей ссылке, чтобы восстановить пароль: $reset_link";
        $headers = "From: spfmaks@gmail.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "На вашу почту отправлено письмо с инструкциями по восстановлению пароля.";
        } else {
            echo "Ошибка отправки email.";
        }
    } else {
        echo "Пользователь с таким email не найден.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Восстановление пароля</title>
</head>
<body>
    <h2>Восстановление пароля</h2>
    <form method="post" action="forgot_password.php">
        <label>Email:</label><input type="email" name="email" required>
        <button type="submit">Отправить</button>
    </form>
</body>
</html>
