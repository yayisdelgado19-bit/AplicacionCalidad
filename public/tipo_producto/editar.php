<?php
require_once "../../config/db.php";

$id = $_GET['id'];

$query = "SELECT * FROM tipo_producto WHERE id = $id";
$result = mysqli_query($conexion, $query);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['actualizar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $update = "UPDATE tipo_producto 
               SET nombre='$nombre', descripcion='$descripcion' 
               WHERE id = $id";

    mysqli_query($conexion, $update);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tipo de Producto</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>

<body>
<div class="container">

    <h1>Editar Tipo de Producto</h1>

    <form method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= $row['nombre'] ?>" required>

        <label>Descripción:</label>
        <input type="text" name="descripcion" value="<?= $row['descripcion'] ?>">

        <button type="submit" name="actualizar" class="btn">Actualizar</button>
        <a href="index.php" class="btn" style="background:#444; margin-left:10px;">Cancelar</a>

    </form>

</div>
</body>
</html>

