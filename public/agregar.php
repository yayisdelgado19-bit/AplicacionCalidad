<?php
session_start();
require_once "../config/db.php";
$tipos=$conexion->query("SELECT * FROM tipos");

if($_POST){
    $nombre=$_POST['nombre'];
    $tipo=$_POST['tipo'];
    $conexion->query("INSERT INTO productos(nombre,tipo_id) VALUES('$nombre','$tipo')");
    header("Location: productos.php");
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="css/style.css"></head>
<body>
<h2>Agregar producto</h2>
<form method="POST">
<input type="text" name="nombre" placeholder="Nombre">
<select name="tipo">
<?php while($t=$tipos->fetch_assoc()){ ?>
<option value="<?= $t['id'] ?>"><?= $t['nombre'] ?></option>
<?php } ?>
</select>
<button class="btn">Guardar</button>
</form>
</body>
</html>