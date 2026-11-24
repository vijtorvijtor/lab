<?php
require_once 'auth.php';
require_once 'db.php';
include 'header.php';

// Manejo de inserciones
foreach (['marcas', 'materiales', 'tratamientos', 'visiones', 'series', 'ubicaciones'] as $t) {
    if (isset($_POST['add_' . $t])) {
        $stmt = $conn->prepare("INSERT INTO $t (nombre) VALUES (?)");
        $stmt->bind_param("s", $_POST['nombre']);
        $stmt->execute();
    }
}

// Manejo de eliminaciones con validación
if (isset($_GET['del'])) {
    $id = (int)$_GET['id'];
    $tabla = $_GET['tabla'];

    // Mapeo dinámico de columnas ID
    $map = [
        'marcas' => 'id_marca',
        'materiales' => 'id_material',
        'tratamientos' => 'id_tratamiento',
        'visiones' => 'id_vision',
        'series' => 'id_serie',
        'ubicaciones' => 'id_ubicacion'
    ];

    $col = $map[$tabla] ?? '';

    if ($col) {
    $check = $conn->query("SELECT count(*) as total FROM micas WHERE $col = $id");
    if ($check->fetch_assoc()['total'] == 0) {
            $stmt = $conn->prepare("DELETE FROM $tabla WHERE $col = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
    } else {
        echo "<div class='alert alert-danger'>No se puede eliminar: hay micas vinculadas.</div>";
    }
}
}
?>

<div class="row g-4">
    <?php 
    $catalogos = [['Marcas', 'marcas', 'id_marca'], ['Materiales', 'materiales', 'id_material'], ['Tratamientos', 'tratamientos', 'id_tratamiento'], ['Visiones', 'visiones', 'id_vision'], ['Series', 'series', 'id_serie'], ['Ubicaciones', 'ubicaciones', 'id_ubicacion']];
    foreach ($catalogos as $cat) { 
        [$titulo, $tabla, $idCol] = $cat; ?>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5><?php echo $titulo; ?></h5>
                <form method="POST" class="mb-3 d-flex gap-1">
                    <input type="text" name="nombre" class="form-control" placeholder="Nuevo nombre" required>
                    <button type="submit" name="add_<?php echo $tabla; ?>" class="btn btn-sm btn-primary">+</button>
                </form>
                <table class="table table-sm">
                    <?php $res = $conn->query("SELECT * FROM $tabla");
                    while($r = $res->fetch_assoc()) echo "<tr><td>{$r['nombre']}</td><td class='text-end'><a href='?del=1&tabla=$tabla&id={$r[$idCol]}' class='btn btn-sm btn-outline-danger'>x</a></td></tr>"; ?>
                </table>
            </div>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>

