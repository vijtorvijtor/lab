<?php
include 'db.php';
include 'menu.php';

if (isset($_POST['guardar_mica'])) {
    $id_grad = (int)$_POST['id_grad'];
    $id_marca = (int)$_POST['id_marca'];
    $id_mat = (int)$_POST['id_material'];
    $id_trat = (int)$_POST['id_tratamiento'];
    $stock = (int)$_POST['stock'];
    $ubicacion = $conn->real_escape_string($_POST['ubicacion']);

    $stmt = $conn->prepare("INSERT INTO micas (id_grad, id_marca, id_material, id_tratamiento, ubicacion, stock_actual) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiisi", $id_grad, $id_marca, $id_mat, $id_trat, $ubicacion, $stock);
    $stmt->execute();
    
    $idMica = $stmt->insert_id;
    $conn->query("INSERT INTO movimientos_inventario (id_mica, tipo_movimiento, cantidad, fecha, comentarios) VALUES ($idMica, 'entrada', $stock, NOW(), 'Alta inicial de material')");
    
    echo "Material dado de alta correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Material</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <h1>Alta de Material</h1>
    <form method="POST">
        <label>Graduación:</label><br>
        <select name="id_grad" class="select-filtrable" style="width: 300px;" required>
            <?php
            $res = $conn->query("SELECT id_grad, esf, cil FROM graduaciones");
            while($r = $res->fetch_assoc()) {
                $label = sprintf("%+0.2f / %+0.2f", $r['esf'], $r['cil']);
                echo "<option value='{$r['id_grad']}'>$label</option>";
            }
            ?>
        </select><br>
        <label>Marca:</label><br>
        <select name="id_marca" class="select-filtrable" style="width: 300px;" required>
            <?php $res = $conn->query("SELECT * FROM marcas"); while($r = $res->fetch_assoc()) echo "<option value='{$r['id_marca']}'>{$r['nombre']}</option>"; ?>
        </select><br>
        <label>Material:</label><br>
        <select name="id_material" class="select-filtrable" style="width: 300px;" required>
            <?php $res = $conn->query("SELECT * FROM materiales"); while($r = $res->fetch_assoc()) echo "<option value='{$r['id_material']}'>{$r['nombre']}</option>"; ?>
        </select><br>
        <label>Tratamiento:</label><br>
        <select name="id_tratamiento" class="select-filtrable" style="width: 300px;" required>
            <?php $res = $conn->query("SELECT * FROM tratamientos"); while($r = $res->fetch_assoc()) echo "<option value='{$r['id_tratamiento']}'>{$r['nombre']}</option>"; ?>
        </select><br>
        <label>Ubicación:</label>
        <input type="text" name="ubicacion" placeholder="Ej: Vitrina 1" required><br>
        <label>Stock Inicial:</label>
        <input type="number" name="stock" required><br>
        <button type="submit" name="guardar_mica">Guardar Mica</button>
    </form>
    <script>
        $(document).ready(function() {
            $('.select-filtrable').select2({
                placeholder: "Buscar...",
                allowClear: true,
                minimumResultsForSearch: 0,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') return data;
                    var terms = params.term.toUpperCase().split(' ');
                    var text = data.text.toUpperCase().replace(/\//g, ' ');
                    var currentIndex = 0;
                    for (var i = 0; i < terms.length; i++) {
                        var foundIndex = text.indexOf(terms[i], currentIndex);
                        if (foundIndex === -1) return null;
                        currentIndex = foundIndex + terms[i].length;
                    }
                    return data;
                }
            });
        });
    </script>
</body>
</html>

