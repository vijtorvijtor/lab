<?php include 'db.php'; include 'menu.php'; 
if(isset($_POST['add_cliente'])) { $conn->query("INSERT INTO clientes (nombre, telefono) VALUES ('".$_POST['nombre']."', '".$_POST['telefono']."')"); }
?>
<h1>Gestión de Clientes</h1>
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="telefono" placeholder="Teléfono">
    <button type="submit" name="add_cliente">Agregar Cliente</button>
</form>
<h3>Clientes actuales:</h3>
<table border="1"><tr><th>Nombre</th><th>Teléfono</th></tr>
<?php $res = $conn->query("SELECT * FROM clientes"); while($r=$res->fetch_assoc()) echo "<tr><td>{$r['nombre']}</td><td>{$r['telefono']}</td></tr>"; ?>
</table>