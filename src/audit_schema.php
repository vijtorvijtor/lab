<?php
$conn = new mysqli('localhost', 'u947744031_lab', '1Abejash(*@', 'u947744031_lab');
$tablas = ['notas', 'partidas', 'micas', 'graduaciones', 'marcas', 'tratamientos'];
foreach ($tablas as $t) {
    echo "--- Tabla: $t ---\n";
    $res = $conn->query("DESCRIBE $t");
    if ($res) {
        while($r = $res->fetch_assoc()) echo $r['Field'] . " ";
    }
    echo "\n\n";
}
?>
