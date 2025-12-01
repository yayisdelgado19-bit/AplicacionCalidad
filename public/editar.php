<?php
// Configuración de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// phpcs:ignore -- Supresión necesaria para include de configuración
require_once "../../config/db.php";

// ✅ Validación completa sin acceso directo a superglobals
$id_raw = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id_raw === null || $id_raw === false || $id_raw === '') {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

$id = filter_var($id_raw, FILTER_VALIDATE_INT, [
    'options' => [
        'min_range' => 1,
        'max_range' => 2147483647
    ]
]);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// ✅ Prepared statement para SELECT
$stmt = $conexion->prepare("SELECT id, nombre, descripcion FROM tipo_producto WHERE id = ? LIMIT 1");

if (!$stmt) {
    error_log("Error preparando consulta: " . $conexion->error);
    http_response_code(500);
    exit("Error del sistema");
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    header("Location: index.php", true, 303);
    exit();
}

$error = null;

// ✅ Procesar actualización con sanitización completa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    
    $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nombre = trim($nombre);
    
    $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $descripcion = trim($descripcion);
    
    if (empty($nombre)) {
        $error = "El nombre es obligatorio";
    } elseif (strlen($nombre) > 255) {
        $error = "El nombre excede el límite de caracteres";
    } else {
        $updateStmt = $conexion->prepare("UPDATE tipo_producto SET nombre = ?, descripcion = ? WHERE id = ? LIMIT 1");
        
        if (!$updateStmt) {
            error_log("Error en UPDATE: " . $conexion->error);
            $error = "Error al actualizar";
        } else {
            $updateStmt->bind_param("ssi", $nombre, $descripcion, $id);
            
            if ($updateStmt->execute() && $updateStmt->affected_rows > 0) {
                $updateStmt->close();
                $conexion->close();
                header("Location: index.php", true, 303);
                exit();
            } else {
                error_log("No se actualizó ningún registro");
                $error = "No se pudo actualizar el registro";
            }
            
            if (isset($updateStmt)) {
                $updateStmt->close();
            }
        }
    }
}

$nombreOutput = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$descripcionOutput = htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Editar tipo de producto</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>
<body>
<div class="container">
    <h1>Editar Tipo de Producto</h1>
    
    <?php if ($error !== null): ?>
        <div class="error" style="color: red; padding: 10px; margin-bottom: 15px; border: 1px solid red; background: #ffe6e6;">
            <?php echo htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" accept-charset="UTF-8" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="nombre">Nombre:</label>
        <input type="text" 
               id="nombre"
               name="nombre" 
               value="<?php echo $nombreOutput; ?>" 
               required
               maxlength="255"
               autocomplete="off">
        
        <label for="descripcion">Descripción:</label>
        <textarea
               id="descripcion"
               name="descripcion"
               maxlength="500"
               rows="4"
               autocomplete="off"><?php echo $descripcionOutput; ?></textarea>
        
        <div style="margin-top: 15px;">
            <button type="submit" name="actualizar" value="1" class="btn">Actualizar</button>
            <a href="index.php" class="btn" style="background:#444;margin-left:10px;">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
