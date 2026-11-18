<?php
include 'db.php';
include 'menu.php';

// Crear nueva nota si se solicita
if (isset($_POST['nueva_nota'])) {
    $idCliente = (int)$_POST['id_cliente'];
    $stmt = $conn->prepare("INSERT INTO notas (fecha_apertura, id_cliente, estatus) VALUES (NOW(), ?, 'abierta')");
    $stmt->bind_param("i", $idCliente);
    $stmt->execute();
    $idNota = $stmt->insert_id;
    header("Location: nota.php?id=" . $idNota);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Laboratorio Óptico</title>
</head>
<body>
    <h1>Nueva Nota de Venta</h1>
    <form method="POST">
        <label>Seleccionar Cliente:</label>
        <select name="id_cliente" required>
            <?php
            $res = $conn->query("SELECT id_cliente, nombre FROM clientes");
            if ($res) {
                while ($row = $res->fetch_assoc()) echo "<option value='{$row['id_cliente']}'>{$row['nombre']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="nueva_nota">Abrir Nota</button>
    </form>
</body>
</html>