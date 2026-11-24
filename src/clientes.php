<?php
require_once 'auth.php';
require_once 'db.php';
include 'header.php';

if (isset($_POST['add_cliente'])) {
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, telefono) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['nombre'], $_POST['telefono']);
    $stmt->execute();
}
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h5>Nuevo Cliente [+]</h5>
            <form method="POST">
                <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
                <input type="text" name="telefono" class="form-control mb-2" placeholder="Teléfono">
                <button type="submit" name="add_cliente" class="btn btn-primary w-100">Registrar</button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-4 shadow-sm">
            <h5>Directorio de Clientes</h5>
            <input type="text" id="filtroNotas" class="form-control mb-3" placeholder="Buscar cliente...">
            <table class="table table-hover" id="tablaNotas">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Nombre</th>
                        <th onclick="sortTable(1)">Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = $conn->query("SELECT nombre, telefono FROM clientes");
                    while($r = $res->fetch_assoc()) echo "<tr><td>{$r['nombre']}</td><td>{$r['telefono']}</td></tr>";
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
