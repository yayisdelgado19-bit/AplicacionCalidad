<?php
require_once "../../config/db.php";

// Validar ID recibido
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("ID inválido.");
}

$id = intval($_GET['id']);

// Consulta segura (PREPARED STATEMENT)
$stmt = $conexion->prepare("SELECT * FROM tipo_producto WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows == 0){
    die("Tipo no encontrado.");
}

$fila = $resultado->fetch_assoc();

// ACTUALIZAR
if(isset($_POST['actualizar'])){
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    if(empty($nombre)){
        die("El nombre no puede estar vacío.");
    }

    // UPDATE seguro
    $update = $conexion->prepare("UPDATE tipo_producto SET nombre = ?, descripcion = ? WHERE id = ?");
    $update->bind_param("ssi", $nombre, $descripcion, $id);
    $update->execute();

    header("Location: index.php");
    exit();
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
        <input type="text" name="nombre" value="<?= htmlspecialchars($fila['nombre']) ?>" required>

        <label>Descripción:</label>
        <input type="text" name="descripcion" value="<?= htmlspecialchars($fila['descripcion']) ?>">

        <button type="submit" name="actualizar" class="btn">Actualizar</button>
        <a href="index.php" class="btn" style="background:#444; margin-left:10px;">Cancelar</a>
    </form>
</div>
</body>
</html>
