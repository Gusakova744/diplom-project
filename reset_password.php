<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверка, совпадают ли пароли
    if ($new_password !== $confirm_password) {
        echo "Пароли не совпадают.";
        exit();
    }

    // Поиск user_id по токену в таблице password_resets
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Обновление пароля пользователя
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
        $stmt->close();

        // Удаление токена из таблицы password_resets
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();

        echo "Ваш пароль был успешно изменен. <a href='login.php'>Войти</a>";
    } else {
        echo "Неверный или истекший токен.";
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    echo "Неверный запрос.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сброс пароля</title>
</head>
<body>
    <h2>Сброс пароля</h2>
    <form method="post" action="reset_password.php">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>Новый пароль:</label><input type="password" name="new_password" required>
        <label>Повторите пароль:</label><input type="password" name="confirm_password" required>
        <button type="submit">Сбросить пароль</button>
    </form>
</body>
</html>
