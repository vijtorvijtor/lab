<?php
require_once 'auth.php';
require_once 'db.php';
include 'header.php';

if (isset($_POST['nueva_nota'])) {
    $idCliente = (int)$_POST['id_cliente'];
    $stmt = $conn->prepare("INSERT INTO notas (fecha_apertura, id_cliente, estatus) VALUES (NOW(), ?, 'abierta')");
    $stmt->bind_param("i", $idCliente);
    $stmt->execute();
    header("Location: nota.php?id=" . $stmt->insert_id);
    exit;
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm p-3">
            <h3>Nueva Nota</h3>
            <form method="POST">
                <select name="id_cliente" class="form-select select-filtrable mb-3" required>
                    <?php 
                    $res = $conn->query("SELECT id_cliente, nombre FROM clientes");
                    while($row = $res->fetch_assoc()) echo "<option value='{$row['id_cliente']}'>{$row['nombre']}</option>";
                    ?>
                </select>
                <button type="submit" name="nueva_nota" class="btn btn-success w-100">Abrir Nueva Nota</button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm p-3">
            <h3>Notas Abiertas</h3>
            <table class="table table-hover">
                <thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Acción</th></tr></thead>
                <tbody>
                    <?php 
                    $res = $conn->query("SELECT n.id_nota, c.nombre, n.fecha_apertura FROM notas n JOIN clientes c ON n.id_cliente = c.id_cliente WHERE n.estatus = 'abierta'");
                    while($row = $res->fetch_assoc()) echo "<tr><td>{$row['id_nota']}</td><td>{$row['nombre']}</td><td>{$row['fecha_apertura']}</td><td><a href='nota.php?id={$row['id_nota']}' class='btn btn-sm btn-primary'>Continuar</a></td></tr>";
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
