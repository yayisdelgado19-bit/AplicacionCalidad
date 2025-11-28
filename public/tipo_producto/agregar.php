<?php
require_once "../../config/db.php";

if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $query = "INSERT INTO tipo_producto (nombre, descripcion) VALUES ('$nombre', '$descripcion')";
    mysqli_query($conexion, $query);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Tipo de Producto</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>

<body>
<div class="container">

    <h1>Agregar Tipo de Producto</h1>

    <form method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Descripción:</label>
        <input type="text" name="descripcion">

        <button type="submit" name="guardar" class="btn">Guardar</button>
        <a href="index.php" class="btn" style="background:#444; margin-left:10px;">Cancelar</a>

    </form>

</div>
</body>
</html>
