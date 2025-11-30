<?php
// Iniciar sesión segura
session_start();

// Configuración de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

require_once "../../config/db.php";

// Validación estricta del ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => [
        'min_range' => 1,
        'max_range' => PHP_INT_MAX
    ]
]);

// Redirección si ID inválido
if ($id === false || $id === null) {
    header("Location: index.php", true, 303);
    exit();
}

// Prepared statement para SELECT
$stmt = $conexion->prepare("SELECT id, nombre, descripcion FROM tipo_producto WHERE id = ? LIMIT 1");

if ($stmt === false) {
    error_log("Error preparando consulta: " . $conexion->error);
    die("Error en el sistema");
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    error_log("Error ejecutando consulta: " . $stmt->error);
    $stmt->close();
    die("Error en el sistema");
}

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Verificar existencia del registro
if (!$row || !is_array($row)) {
    header("Location: index.php", true, 303);
    exit();
}

$error = null;

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verificar token CSRF (opcional pero recomendado)
    // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    //     die("Token CSRF inválido");
    // }
    
    if (isset($_POST['actualizar'])) {
        // Sanitizar y validar inputs
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        
        // Validaciones
        if (empty($nombre)) {
            $error = "El nombre es obligatorio";
        } elseif (strlen($nombre) > 255) {
            $error = "El nombre es demasiado largo";
        } elseif (strlen($descripcion) > 500) {
            $error = "La descripción es demasiado larga";
        } else {
            // Prepared statement para UPDATE
            $updateStmt = $conexion->prepare("UPDATE tipo_producto SET nombre = ?, descripcion = ? WHERE id = ? LIMIT 1");
            
            if ($updateStmt === false) {
                error_log("Error preparando update: " . $conexion->error);
                $error = "Error en el sistema";
            } else {
                $updateStmt->bind_param("ssi", $nombre, $descripcion, $id);
                
                if ($updateStmt->execute()) {
                    $updateStmt->close();
                    $conexion->close();
                    header("Location: index.php", true, 303);
                    exit();
                } else {
                    error_log("Error ejecutando update: " . $updateStmt->error);
                    $error = "Error al actualizar el registro";
                    $updateStmt->close();
                }
            }
        }
    }
}

// Generar token CSRF (opcional)
// if (!isset($_SESSION['csrf_token'])) {
//     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// }

// Escapar datos para output
$nombre_safe = htmlspecialchars($row['nombre'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$descripcion_safe = htmlspecialchars($row['descripcion'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
            <?php echo htmlspecialchars($error, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" accept-charset="UTF-8">
        <!-- Token CSRF opcional -->
        <!-- <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"> -->
        
        <label for="nombre">Nombre:</label>
        <input type="text" 
               id="nombre"
               name="nombre" 
               value="<?php echo $nombre_safe; ?>" 
               required
               maxlength="255"
               pattern="[A-Za-z0-9\s\-áéíóúÁÉÍÓÚñÑ]+"
               title="Solo letras, números, espacios y guiones">
        
        <label for="descripcion">Descripción:</label>
        <textarea
               id="descripcion"
               name="descripcion"
               maxlength="500"
               rows="4"><?php echo $descripcion_safe; ?></textarea>
        
        <button type="submit" name="actualizar" class="btn">Actualizar</button>
        <a href="index.php" class="btn" style="background:#444;margin-left:10px;">Cancelar</a>
    </form>
</div>
</body>
</html>
