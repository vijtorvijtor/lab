<?php
$host = "localhost";
$dbname = "u947744031_lab";
$user = "u947744031_lab";
$pass = "1Abejash(*@";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>