<?php
include 'db.php';
$u = 'a'; // Probamos con 'a'
$stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $u);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res) {
    echo "Usuario encontrado.<br>";
    echo "Hash en DB: " . $res['password'] . "<br>";
    echo "Hash generado con '1': " . password_hash('1', PASSWORD_DEFAULT) . "<br>";
    if (password_verify('1', $res['password'])) {
        echo "<b>¡La verificación funciona! El problema es otro.</b>";
    } else {
        echo "<b>La verificación falla. El hash no coincide.</b>";
    }
} else {
    echo "Usuario 'a' no encontrado.";
}
?>