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

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение оплаты</title>
</head>
<body>
    <h2>Подтверждение оплаты</h2>
    <form method="post" action="process_payment.php">
        <label>Сумма к оплате: </label><?php echo $amount; ?> руб.
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <button type="submit">Оплатить <?php echo $amount; ?> руб.</button>
    </form>
</body>
</html>
