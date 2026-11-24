<?php
require_once 'auth.php';
require_once 'db.php';
include 'header.php';

$idNota = (int)$_GET['id'];
$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador');

// Función de formato estricto
function formatoGrad($e, $c, $ej, $a) {
    $g = sprintf("%+0.2f / %+0.2f x %d", $e, $c, $ej);
    if ((float)$a != 0.00) $g .= " Add " . sprintf("%+0.2f", $a);
    return $g;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesamiento de Estado de Nota
    if (isset($_POST['update_estatus']) && $esAdmin) {
        $nuevoEstatus = $_POST['estatus'];
        $conn->query("UPDATE notas SET estatus = '$nuevoEstatus' WHERE id_nota = $idNota");
    }

if (isset($_POST['add_mica'])) {
    $idMica = (int)$_POST['id_mica'];
    $cantidad = (int)$_POST['cantidad'];
    $conn->query("INSERT INTO partidas (id_nota, tipo_partida, id_mica, cantidad, descripcion, precio_unitario, fecha_partida)
                      SELECT $idNota, 'mica', m.id_mica, $cantidad, CONCAT(mar.nombre, ' ', m.serie, ' ', m.vision), m.precio_venta, NOW()
                      FROM micas m JOIN marcas mar ON m.id_marca = mar.id_marca WHERE m.id_mica = $idMica");
    // Reducir stock
    $conn->query("UPDATE micas SET stock_actual = stock_actual - $cantidad WHERE id_mica = $idMica");
}
if (isset($_POST['add_servicio'])) {
    $tipo = $_POST['tipo_servicio'];
    $precio = (float)$_POST['precio'];
        $conn->query("INSERT INTO partidas (id_nota, tipo_partida, descripcion, precio_unitario, cantidad, fecha_partida) VALUES ($idNota, '$tipo', '$tipo', $precio, 1, NOW())");
}
if (isset($_POST['add_abono'])) {
    $monto = (float)$_POST['monto'];
    $conn->query("INSERT INTO abonos (id_nota, monto, fecha, forma_pago) VALUES ($idNota, $monto, NOW(), 'Efectivo')");
}
    if (isset($_POST['del_partida']) && $esAdmin) {
        $idPartida = (int)$_POST['id_partida'];
        $conn->query("DELETE FROM partidas WHERE id_partida = $idPartida AND id_nota = $idNota");
    }
}

// Obtener detalles de la nota
$resNota = $conn->query("SELECT n.*, c.nombre as cliente FROM notas n JOIN clientes c ON n.id_cliente = c.id_cliente WHERE n.id_nota = $idNota");
$nota = $resNota->fetch_assoc();

if (!$nota) { die("Nota no encontrada."); }

// Obtener totales
$resTotales = $conn->query("SELECT SUM(precio_unitario * cantidad) as total FROM partidas WHERE id_nota = $idNota");
$totalVenta = $resTotales->fetch_assoc()['total'] ?? 0;

$resAbonos = $conn->query("SELECT SUM(monto) as total_abonos FROM abonos WHERE id_nota = $idNota");
$totalAbonos = $resAbonos->fetch_assoc()['total_abonos'] ?? 0;
$saldoPendiente = $totalVenta - $totalAbonos;
?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4">Nota #<?php echo $idNota; ?> <small class="text-muted">| Cliente: <?php echo $nota['cliente']; ?></small></h3>
            <table class="table table-hover" id="tablaPartidas">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Tipo</th>
                <th onclick="sortTable(1)">Descripción</th>
                <th>Fecha</th>
                <th>Precio</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $partidas = $conn->query("SELECT * FROM partidas WHERE id_nota = $idNota");
                    while($row = $partidas->fetch_assoc()) {
                        echo "<tr><td>{$row['tipo_partida']}</td><td>{$row['descripcion']}</td><td>{$row['fecha_partida']}</td><td>$".number_format($row['precio_unitario'], 2)."</td><td>";
                        if($esAdmin) {
                            echo '<form method="POST"><input type="hidden" name="id_partida" value="'.$row['id_partida'].'"><button type="submit" name="del_partida" class="btn btn-sm btn-danger">x</button></form>';
                        }
                        echo "</td></tr>";
                    }
            ?>
        </tbody>
    </table>
        </div>
    </div>

    <div class="col-md-4">
        <form method="POST" class="card p-3 mb-3">
            <label>Estado de Nota:</label>
            <select name="estatus" class="form-select">
                <option value="abierta" <?php echo $nota['estatus'] == 'abierta' ? 'selected' : ''; ?>>Abierta</option>
                <option value="cerrada" <?php echo $nota['cerrada'] == 'cerrada' ? 'selected' : ''; ?>>Cerrada</option>
                <option value="cancelada" <?php echo $nota['estatus'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
        </select>
            <button type="submit" name="update_estatus" class="btn btn-warning mt-2">Actualizar Estado</button>
    </form>

        <div class="card p-4 shadow-sm mb-3">
            <h5>Resumen</h5>
            <p>Total: $<?php echo number_format($totalVenta, 2); ?></p>
            <p>Pagado: $<?php echo number_format($totalAbonos, 2); ?></p>
            <h4 class="text-primary">Saldo: $<?php echo number_format($saldoPendiente, 2); ?></h4>
        </div>

        <div class="card p-4 shadow-sm mb-3">
            <h5>Registrar Abono</h5>
            <form method="POST" class="d-flex gap-2">
                <input type="number" name="monto" class="form-control" step="0.01" required>
                <button type="submit" name="add_abono" class="btn btn-success">Pagar</button>
    </form>
        </div>

        <div class="card p-4 shadow-sm mb-3">
            <h5>Agregar Servicio</h5>
            <form method="POST" class="d-flex flex-column gap-2">
            <select name="tipo_servicio" class="form-select">
                <option value="Bisel">Bisel</option><option value="Reparacion">Reparación</option>
                <option value="Taladro">Taladro</option><option value="Facetado">Facetado</option>
                <option value="Ranurado">Ranurado</option>
            </select>
                <input type="number" name="precio" class="form-control" step="0.01" placeholder="Precio" required>
                <button type="submit" name="add_servicio" class="btn btn-warning">Agregar Servicio</button>
            </form>
        </div>

        <div class="card p-4 mb-3">
            <h5>Agregar Mica</h5>
    <form method="POST">
                <select name="id_mica" class="select-filtrable form-select" required>
                    <option value="">Seleccione mica...</option>
                    <?php
                    $m = $conn->query("SELECT m.*, mar.nombre as marca, t.nombre as tratamiento, g.esf, g.cil, g.eje, g.adi
                                       FROM micas m
                                       JOIN marcas mar ON m.id_marca = mar.id_marca
                                       JOIN graduaciones g ON m.id_grad = g.id_grad
                                       JOIN tratamientos t ON m.id_tratamiento = t.id_tratamiento");
                    while($r = $m->fetch_assoc()) {
                        $grad = sprintf("%+0.2f / %+0.2f x %d", $r['esf'], $r['cil'], $r['eje']);
                        if ((float)$r['adi'] != 0.00) $grad .= " Add " . sprintf("%+0.2f", $r['adi']);
                        $label = "{$r['marca']} | {$r['serie']} | {$grad} | {$r['tratamiento']} | {$r['vision']}";
                echo "<option value='{$r['id_mica']}'>$label</option>";
            }?>
        </select>
                <input type="number" name="cantidad" class="form-control mt-2 mb-2" value="1" min="1" required>
                <button type="submit" name="add_mica" class="btn btn-primary w-100">Agregar Mica</button>
    </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select-filtrable').select2({
                placeholder: "Buscar...",
                allowClear: true
            });
        });
        function sortTable(n) {
          var table = document.getElementById("tablaPartidas");
          var rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
          switching = true;
          dir = "asc";
          while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
              shouldSwitch = false;
              x = rows[i].getElementsByTagName("TD")[n];
              y = rows[i + 1].getElementsByTagName("TD")[n];
              if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) { shouldSwitch = true; break; }
              } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) { shouldSwitch = true; break; }
              }
            }
            if (shouldSwitch) {
              rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
              switching = true;
              switchcount ++;
            } else {
              if (switchcount == 0 && dir == "asc") { dir = "desc"; switching = true; }
            }
          }
        }
    </script>

