<?php include 'db.php'; include 'menu.php'; 
if(isset($_POST['add_marca'])) { $conn->query("INSERT INTO marcas (nombre) VALUES ('".$_POST['nombre']."')"); }
?>
<h1>Gestión de Catálogos</h1>
<form method="POST">
    <input type="text" name="nombre" placeholder="Nueva Marca" required>
    <button type="submit" name="add_marca">Agregar Marca</button>
</form>
<h3>Marcas actuales:</h3>
<ul><?php $res = $conn->query("SELECT * FROM marcas"); while($r=$res->fetch_assoc()) echo "<li>{$r['nombre']}</li>"; ?></ul>