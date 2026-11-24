<?php
require_once 'auth.php';
require_once 'db.php';

// Manejo AJAX
if (isset($_POST['ajax'])) {
    $stmt = $conn->prepare("INSERT INTO micas (id_grad, id_marca, id_material, id_tratamiento, id_ubicacion, stock_actual, id_vision, id_serie) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiiii", $_POST['id_grad'], $_POST['id_marca'], $_POST['id_material'], $_POST['id_tratamiento'], $_POST['id_ubicacion'], $_POST['cantidad'], $_POST['id_vision'], $_POST['id_serie']);
    $stmt->execute();
    exit('OK');
}

include 'header.php';
?>

<form id="formInventario" class="card p-4 shadow-sm mb-4">
    <h5 class="mb-3">Configuración de Lote (Comunes)</h5>
    <div class="row mb-4">
        <div class="col-md-2"><select name="id_marca" class="select-filtrable form-select"><?php $res = $conn->query("SELECT * FROM marcas"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id_marca']}'>{$r['nombre']}</option>"; ?></select></div>
        <div class="col-md-2"><select name="id_material" class="select-filtrable form-select"><?php $res = $conn->query("SELECT * FROM materiales"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id_material']}'>{$r['nombre']}</option>"; ?></select></div>
        <div class="col-md-2"><select name="id_tratamiento" class="select-filtrable form-select"><?php $res = $conn->query("SELECT * FROM tratamientos"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id_tratamiento']}'>{$r['nombre']}</option>"; ?></select></div>
        <div class="col-md-2"><select name="id_vision" class="select-filtrable form-select"><?php $res = $conn->query("SELECT * FROM visiones"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id_vision']}'>{$r['nombre']}</option>"; ?></select></div>
        <div class="col-md-2"><select name="id_serie" class="select-filtrable form-select"><?php $res = $conn->query("SELECT * FROM series"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id_serie']}'>{$r['nombre']}</option>"; ?></select></div>
        <div class="col-md-2"><select name="id_ubicacion" class="select-filtrable form-select"><?php $res = $conn->query("SELECT * FROM ubicaciones"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id_ubicacion']}'>{$r['nombre']}</option>"; ?></select></div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <select name="id_grad" class="select-filtrable form-select" required>
                <?php
                $res = $conn->query("SELECT id_grad, esf, cil, eje, adi FROM graduaciones");
                    while($r = $res->fetch_assoc()) {
                $g = sprintf("%+0.2f / %+0.2f x %d", $r['esf'], $r['cil'], $r['eje']);
                if ((float)$r['adi'] != 0.00) $g .= " Add " . sprintf("%+0.2f", $r['adi']);
                    echo "<option value='{$r['id_grad']}'>$g</option>";
                } ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="cantidad" class="form-control" placeholder="Cantidad" required>
        </div>
        <div class="col-md-2">
            <button type="button" id="btnGuardar" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form>

<script>
document.getElementById('btnGuardar').addEventListener('click', function() {
    let form = document.getElementById('formInventario');
    let formData = new FormData(form);
        formData.append('ajax', '1');
        fetch('', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if (data === 'OK') {
                alert('Guardado correctamente');
                }
            });
});
</script>
<?php include 'footer.php'; ?>
            });
});
</script>
<?php include 'footer.php'; ?>


