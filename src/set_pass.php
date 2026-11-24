<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Óptico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<?php include 'menu.php'; ?>
<div class="container mt-4">
<?php
include 'db.php';
$pass = password_hash('1', PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $pass);
if($stmt->execute()) {
    echo "Contraseña actualizada correctamente con hash profesional.";
} else {
    echo "Error: " . $conn->error;
}
?>
</div>
</body>
</html>
