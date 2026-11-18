<?php
include 'db.php';
include 'menu.php';
$idNota = (int)$_GET['id'];

// Obtener detalles de la nota
$resNota = $conn->query("SELECT n.*, c.nombre as cliente FROM notas n JOIN clientes c ON n.id_cliente = c.id_cliente WHERE n.id_nota = $idNota");
$nota = $resNota->fetch_assoc();

// Lógica para agregar partida (mica)
if (isset($_POST['add_mica'])) {
    $idMica = (int)$_POST['id_mica'];
    $conn->query("INSERT INTO partidas (id_nota, tipo_partida, id_mica, cantidad, descripcion, precio_unitario)
                  SELECT $idNota, 'mica', id_mica, 1, CONCAT(m.serie, ' ', g.esf), m.precio_venta
                  FROM micas m JOIN graduaciones g ON m.id_grad = g.id_grad WHERE id_mica = $idMica");
    // Reducir stock
    $conn->query("UPDATE micas SET stock_actual = stock_actual - 1 WHERE id_mica = $idMica");
}

// Lógica para agregar servicio
if (isset($_POST['add_servicio'])) {
    $desc = $conn->real_escape_string($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $conn->query("INSERT INTO partidas (id_nota, tipo_partida, id_mica, cantidad, descripcion, precio_unitario)
                  VALUES ($idNota, 'servicio', NULL, 1, '$desc', $precio)");
}

// Lógica para registrar abono
if (isset($_POST['add_abono'])) {
    $monto = (float)$_POST['monto'];
    $conn->query("INSERT INTO abonos (id_nota, monto, fecha, forma_pago) VALUES ($idNota, $monto, NOW(), 'Efectivo')");
}

// Obtener totales
$resTotales = $conn->query("SELECT SUM(precio_unitario * cantidad) as total FROM partidas WHERE id_nota = $idNota");
$totalVenta = $resTotales->fetch_assoc()['total'] ?? 0;

$resAbonos = $conn->query("SELECT SUM(monto) as total_abonos FROM abonos WHERE id_nota = $idNota");
$totalAbonos = $resAbonos->fetch_assoc()['total_abonos'] ?? 0;
$saldoPendiente = $totalVenta - $totalAbonos;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota #<?php echo $idNota; ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>
    <h1>Nota de Venta #<?php echo $idNota; ?> - Cliente: <?php echo $nota['cliente']; ?></h1>
    
    <h2>Partidas Actuales</h2>
    <table border="1">
        <thead><tr><th>Descripción</th><th>Precio</th><th>Cantidad</th></tr></thead>
        <tbody>
            <?php
            $partidas = $conn->query("SELECT * FROM partidas WHERE id_nota = $idNota");
            while($p = $partidas->fetch_assoc()) echo "<tr><td>{$p['descripcion']}</td><td>{$p['precio_unitario']}</td><td>{$p['cantidad']}</td></tr>";
            ?>
        </tbody>
    </table>

    <h2>Resumen Financiero</h2>
    <p>Total Venta: $<?php echo number_format($totalVenta, 2); ?></p>
    <p>Total Abonos: $<?php echo number_format($totalAbonos, 2); ?></p>
    <p><strong>Saldo Pendiente: $<?php echo number_format($saldoPendiente, 2); ?></strong></p>

    <h2>Registrar Abono</h2>
    <form method="POST">
        <input type="number" name="monto" placeholder="Monto" step="0.01" required>
        <button type="submit" name="add_abono">Registrar Pago</button>
    </form>

    <h2>Agregar Mica</h2>
    <form method="POST">
        <select name="id_mica" class="select-filtrable" style="width: 300px;">
            <?php $m = $conn->query("SELECT m.id_mica, m.serie, g.esf, g.cil FROM micas m JOIN graduaciones g ON m.id_grad = g.id_grad");
            while($r=$m->fetch_assoc()) {
                $label = $r['serie'] . " | " . sprintf("%+0.2f / %+0.2f", $r['esf'], $r['cil']);
                echo "<option value='{$r['id_mica']}'>$label</option>";
            }?>
        </select>
        <button type="submit" name="add_mica">Agregar</button>
    </form>

    <h2>Agregar Servicio</h2>
    <form method="POST">
        <input type="text" name="descripcion" placeholder="Ej: Biselado" required>
        <input type="number" name="precio" placeholder="Precio" step="0.01" required>
        <button type="submit" name="add_servicio">Agregar Servicio</button>
    </form>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select-filtrable').select2({
                placeholder: "Buscar: +2 -1",
                allowClear: true,
                minimumResultsForSearch: 0,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') return data;

                    // Escapamos caracteres especiales para el Regex y separamos por espacios
                    var terms = params.term.toUpperCase().split(' ');

                    // Convertimos la opción a formato limpio para búsqueda (quitamos diagonales)
                    var text = data.text.toUpperCase().replace(/\//g, ' ');

                    // Construimos una regex: término1 seguido de cualquier cosa, luego término2, etc.
                    // Usamos .* para permitir cualquier cosa entre los términos, pero en ese orden.
                    var pattern = terms.map(function(t) {
                        return t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // Escapar regex
                    }).join('.*');

                    var regex = new RegExp(pattern);

                    return regex.test(text) ? data : null;
                }
            });
        });
    </script>
</body>
</html>