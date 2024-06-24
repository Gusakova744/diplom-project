<?php
// Подключение конфигурационного файла
include 'config.php';
session_start(); // Старт сессии

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Перенаправление на страницу логина, если не авторизован
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение данных пользователя
$stmt = $conn->prepare("SELECT account_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($account_number);
$stmt->fetch();
$stmt->close();

// Получение истории показаний счетчика
$stmt = $conn->prepare("SELECT reading, date, is_paid FROM meter_readings WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reading, $date, $is_paid);
$readings = [];
while ($stmt->fetch()) {
    $readings[] = ['reading' => $reading, 'date' => $date, 'is_paid' => $is_paid];
}
$stmt->close();

// Получение истории оплат
$stmt = $conn->prepare("SELECT amount, payment_date, status FROM payments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($amount, $payment_date, $status);
$payments = [];
while ($stmt->fetch()) {
    $payments[] = ['amount' => $amount, 'payment_date' => $payment_date, 'status' => $status];
}
$stmt->close();

// Получение текущей цены за 1 кВт/час
$stmt = $conn->prepare("SELECT price_per_kw FROM prices ORDER BY effective_date DESC LIMIT 1");
$stmt->execute();
$stmt->bind_result($price_per_kw);
$stmt->fetch();
$stmt->close();

// Расчет общей суммы к оплате для неоплаченных показаний
$total_amount_to_pay = 0;
$previous_reading = 0;
foreach ($readings as $index => $reading) {
    if (!$reading['is_paid']) {
        $previous_reading = isset($readings[$index + 1]) ? $readings[$index + 1]['reading'] : 0;
        $total_amount_to_pay += ($reading['reading'] - $previous_reading) * $price_per_kw;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
</head>
<body>
    <h1>Добро пожаловать в личный кабинет</h1>
    <p>Ваш номер лицевого счета: <?php echo $account_number; ?></p>

    <h2>Передать показания счетчика</h2>
    <form method="post" action="submit_reading.php">
        <label>Показания:</label><input type="number" step="1" min="0" name="reading" required>
        <button type="submit">Отправить</button>
    </form>

    <h2>История оплат</h2>
    <ul>
        <?php if (empty($payments)): ?>
            <li>Не найдено данных</li>
        <?php else: ?>
            <?php foreach ($payments as $payment): ?>
                <li><?php echo $payment['payment_date'] . ": " . $payment['amount'] . " - " . $payment['status']; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <h2>История показаний счетчика</h2>
    <ul>
        <?php if (empty($readings)): ?>
            <li>Не найдено данных</li>
        <?php else: ?>
            <?php foreach ($readings as $reading): ?>
                <li><?php echo $reading['date'] . ": " . $reading['reading']; ?> - <?php echo $reading['is_paid'] ? "Оплачено" : "Не оплачено"; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <h2>Оплатить счет</h2>
    <form method="post" action="payment.php">
        <label>Сумма к оплате: </label><?php echo $total_amount_to_pay; ?> руб.
        <input type="hidden" name="amount" value="<?php echo $total_amount_to_pay; ?>">
        <button type="submit">Оплатить</button>
    </form>
</body>
</html>
