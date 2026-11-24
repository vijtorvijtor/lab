<?php 
include 'db.php';
session_start();
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && password_verify($p, $res['password'])) {
        $_SESSION['usuario_id'] = $res['id_usuario'];
        $_SESSION['rol'] = $res['rol'];
        header("Location: index.php");
    } else { echo "Credenciales incorrectas"; }
}
?>
<form method="POST">
    <input type="text" name="username" required placeholder="Usuario">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit" name="login">Ingresar</button>
</form>