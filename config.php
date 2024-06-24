<?php
// Параметры подключения к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donenergo";

// Создание подключения к базе данных
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
