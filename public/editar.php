<?php
require_once "../../config/db.php";

// Validación y sanitización del ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Si el ID no es válido, redirigir
if ($id === false || $id === null || $id <= 0) {
    header("Location: index.php");
    exit();
}

// Consulta segura
$stmt = $conexion->prepare("SELECT * FROM tipo_producto WHERE id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();

// Verificar que existe el registro
if (!$fila) {
    header("Location: index.php");
    exit();
}

$stmt->close();

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    // Validar y sanitizar inputs
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    // Validaciones
    if (empty($nombre)) {
        $error = "El nombre es obligatorio";
    } else {
        // Update seguro
        $update = $conexion->prepare("
            UPDATE tipo_producto 
            SET nombre = ?, descripcion = ?
            WHERE id = ?
        ");
        
        if (!$update) {
            die("Error en la preparación del update: " . $conexion->error);
        }
        
        $update->bind_param("ssi", $nombre, $descripcion, $id);
        
        if ($update->execute()) {
            $update->close();
            $conexion->close();
            header("Location: index.php");
            exit();
        } else {
            $error = "Error al actualizar: " . $update->error;
            $update->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar tipo de producto</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>
<body>
<div class="container">
    <h1>Editar Tipo de Producto</h1>
    
    <?php if (isset($error)): ?>
        <div class="error" style="color: red; margin-bottom: 15px;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <label>Nombre:</label>
        <input type="text" 
               name="nombre" 
               value="<?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>" 
               required
               maxlength="255">
        
        <label>Descripción:</label>
        <input type="text" 
               name="descripcion" 
               value="<?= htmlspecialchars($fila['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
               maxlength="500">
        
        <button type="submit" name="actualizar" class="btn">Actualizar</button>
        <a href="index.php" class="btn" style="background:#444;margin-left:10px;">Cancelar</a>
    </form>
</div>
</body>
</html>
