<?php
require_once "../../config/db.php";

$id = $_GET['id'];

// 1. Consulta segura con SENTENCIA PREPARADA
$stmt = $conexion->prepare("SELECT * FROM tipo_producto WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();

// 2. Actualizar registro
if(isset($_POST['actualizar'])) {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $stmt2 = $conexion->prepare("UPDATE tipo_producto SET nombre = ?, descripcion = ? WHERE id = ?");
    $stmt2->bind_param("ssi", $nombre, $descripcion, $id);
    $stmt2->execute();

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar tipo de producto</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>
<body>
<div class="container">
    <h1>Editar Tipo de Producto</h1>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= $fila['nombre'] ?>" required>

        <label>Descripción:</label>
        <input type="text" name="descripcion" value="<?= $fila['descripcion'] ?>">

        <button type="submit" name="actualizar" class="btn">Actualizar</button>
        <a href="index.php" class="btn" style="background:#444; margin-left:10px;">Cancelar</a>
    </form>
</div>
</body>
</html>
