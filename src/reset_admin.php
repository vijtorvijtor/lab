<?php
include 'db.php';
$pass = password_hash('admin123', PASSWORD_DEFAULT);
$query = "UPDATE usuarios SET password = '$pass' WHERE username = 'admin'";
if ($conn->query($query)) {
    echo "Usuario 'admin' reseteado a 'admin123' exitosamente.";
} else {
    echo "Error: " . $conn->error;
}
?>