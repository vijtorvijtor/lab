<?php
// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$host = "localhost";
$dbname = "u947744031_lab";
$user = "u947744031_lab";
$pass = "1Abejash(*@";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$debug_info = "DEBUG: Cargado " . basename($_SERVER['PHP_SELF']) . " | Modificado: " . date("H:i:s", filemtime($_SERVER['SCRIPT_FILENAME']));
?>
<div style="background:#ffeb3b; padding:5px; font-size:12px; text-align:center; font-family:monospace;">
    <?php echo $debug_info; ?>
</div>
<nav style="background:#333; color:#fff; padding:10px; margin-bottom:20px;">
    <a href="index.php?v=<?php echo time(); ?>" style="color:white; margin-right:15px;">Nueva Nota</a>
    <a href="alta_material.php?v=<?php echo time(); ?>" style="color:white; margin-right:15px;">Inventario</a>
    <a href="catalogos.php?v=<?php echo time(); ?>" style="color:white; margin-right:15px;">Catálogos</a>
    <a href="clientes.php?v=<?php echo time(); ?>" style="color:white; margin-right:15px;">Clientes</a>
</nav>
