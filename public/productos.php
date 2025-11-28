<?php
session_start();
if(!isset($_SESSION['login'])){ header("Location: login.php"); exit; }
require_once "../config/db.php";
$result=$conexion->query("SELECT productos.id, productos.nombre, tipos.nombre AS tipo FROM productos JOIN tipos ON tipos.id=productos.tipo_id");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/productos.css">
<title>Productos</title>
</head>
<body>
<h1>Productos</h1>
<a class='btn' href="agregar.php">Agregar</a>
<a class='btn' href="tipo_producto/agregar.php">Agregar Tipo de Producto</a>

<table>
<tr><th>Nombre</th><th>Tipo</th><th>Acciones</th></tr>
<?php while($row=$result->fetch_assoc()){ ?>
<tr>
<td><?= $row['nombre'] ?></td>
<td><?= $row['tipo'] ?></td>
<td>
<a href='editar.php?id=<?= $row['id'] ?>' class='btn small'>Editar</a>
<a href='eliminar.php?id=<?= $row['id'] ?>' class='btn small red'>Eliminar</a>
</td>
</tr>
<?php } ?>
</table>
</body>
</html>