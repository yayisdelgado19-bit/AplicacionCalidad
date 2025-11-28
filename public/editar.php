<?php
session_start();
require_once "../config/db.php";

$id=$_GET['id'];
$producto=$conexion->query("SELECT * FROM productos WHERE id=$id")->fetch_assoc();
$tipos=$conexion->query("SELECT * FROM tipos");

if($_POST){
    $nombre=$_POST['nombre'];
    $tipo=$_POST['tipo'];
    $conexion->query("UPDATE productos SET nombre='$nombre', tipo_id='$tipo' WHERE id=$id");
    header("Location: productos.php");
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="css/style.css"></head>
<body>
<h2>Editar producto</h2>
<form method="POST">
<input type="text" name="nombre" value="<?= $producto['nombre'] ?>">
<select name="tipo">
<?php while($t=$tipos->fetch_assoc()){ ?>
<option value="<?= $t['id'] ?>" <?= $t['id']==$producto['tipo_id']?"selected":"" ?>><?= $t['nombre'] ?></option>
<?php } ?>
</select>
<button class="btn">Actualizar</button>
</form>
</body>
</html>