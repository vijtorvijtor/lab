<?php
require_once 'auth.php';
require_once 'db.php';

// Lógica de descarga CSV
if (isset($_GET['export'])) {
    $tipo = $_GET['export'];
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reporte_'.$tipo.'.csv"');
    $output = fopen('php://output', 'w');

    if ($tipo == 'ventas') {
        fputcsv($output, ['ID Nota', 'Cliente', 'Fecha', 'Estatus']);
        $res = $conn->query("SELECT n.id_nota, c.nombre, n.fecha_apertura, n.estatus FROM notas n JOIN clientes c ON n.id_cliente = c.id_cliente");
        while($r = $res->fetch_assoc()) fputcsv($output, $r);
    } elseif ($tipo == 'inventario') {
        fputcsv($output, ['Graduación', 'Marca', 'Ubicación', 'Stock']);
        $res = $conn->query("SELECT CONCAT(g.esf, '/', g.cil), mar.nombre, m.ubicacion, m.stock_actual FROM micas m JOIN graduaciones g ON m.id_grad = g.id_grad JOIN marcas mar ON m.id_marca = mar.id_marca");
        while($r = $res->fetch_assoc()) fputcsv($output, $r);
    }
    exit;
}

include 'header.php';
?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm">
            <h5>Reporte de Ventas</h5>
            <p>Descargar historial completo de notas.</p>
            <a href="?export=ventas" class="btn btn-success w-100">Descargar CSV</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 shadow-sm">
            <h5>Reporte de Inventario</h5>
            <p>Descargar existencias actuales de micas.</p>
            <a href="?export=inventario" class="btn btn-primary w-100">Descargar CSV</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>