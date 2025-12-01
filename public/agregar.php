<?php
// Configuración de sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Headers de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php", true, 303);
    exit();
}

// Validar y cargar configuración
$path = realpath(__DIR__ . '/../config/db.php');
if ($path === false) {
    http_response_code(500);
    exit('Error: No se encontró el archivo de configuración.');
}
require_once $path;

$error = null;
$success = null;

// ✅ Obtener tipos con prepared statement
$stmt_tipos = $conexion->prepare("SELECT id, nombre FROM tipos ORDER BY nombre ASC");
if (!$stmt_tipos) {
    error_log("Error preparando consulta tipos: " . $conexion->error);
    http_response_code(500);
    exit("Error del sistema");
}

$stmt_tipos->execute();
$tipos = $stmt_tipos->get_result();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ✅ Validación y sanitización de inputs
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nombre = trim($nombre);
    
    $tipo_raw = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_NUMBER_INT);
    
    // Validar tipo como entero
    $tipo = filter_var($tipo_raw, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    
    // Validaciones
    if (empty($nombre)) {
        $error = "El nombre del producto es obligatorio";
    } elseif (strlen($nombre) > 255) {
        $error = "El nombre es demasiado largo (máximo 255 caracteres)";
    } elseif ($tipo === false || $tipo === null) {
        $error = "Debe seleccionar un tipo válido";
    } else {
        
        // ✅ Verificar que el tipo existe en la BD
        $verify_stmt = $conexion->prepare("SELECT id FROM tipos WHERE id = ? LIMIT 1");
        if (!$verify_stmt) {
            error_log("Error verificando tipo: " . $conexion->error);
            $error = "Error del sistema";
        } else {
            $verify_stmt->bind_param("i", $tipo);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            
            if ($verify_result->num_rows === 0) {
                $error = "El tipo seleccionado no es válido";
                $verify_stmt->close();
            } else {
                $verify_stmt->close();
                
                // ✅ Prepared statement para INSERT (previene SQL Injection)
                $insert_stmt = $conexion->prepare("INSERT INTO productos (nombre, tipo_id) VALUES (?, ?)");
                
                if (!$insert_stmt) {
                    error_log("Error preparando INSERT: " . $conexion->error);
                    $error = "Error al guardar el producto";
                } else {
                    $insert_stmt->bind_param("si", $nombre, $tipo);
                    
                    if ($insert_stmt->execute()) {
                        $insert_stmt->close();
                        $conexion->close();
                        header("Location: productos.php?status=created", true, 303);
                        exit();
                    } else {
                        error_log("Error ejecutando INSERT: " . $insert_stmt->error);
                        $error = "Error al guardar el producto";
                        $insert_stmt->close();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Agregar Producto - Sistema de Gestión</title>
</head>
<body>
<div class="form-container">
    <h2>Agregar producto</h2>
    
    <?php if ($error !== null): ?>
        <div class="error" style="color: red; padding: 10px; margin-bottom: 15px; border: 1px solid red; background: #ffe6e6;">
            <?php echo htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" autocomplete="off">
        <label for="nombre">Nombre del producto:</label>
        <input type="text" 
               id="nombre"
               name="nombre" 
               placeholder="Nombre del producto"
               required
               maxlength="255"
               value="<?php echo isset($nombre) ? htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') : ''; ?>">
        
        <label for="tipo">Tipo de producto:</label>
        <select name="tipo" id="tipo" required>
            <option value="">-- Seleccione un tipo --</option>
            <?php 
            if ($tipos->num_rows > 0) {
                while ($t = $tipos->fetch_assoc()) { 
                    $selected = (isset($tipo) && $tipo == $t['id']) ? 'selected' : '';
            ?>
                <option value="<?php echo htmlspecialchars($t['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>>
                    <?php echo htmlspecialchars($t['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                </option>
            <?php 
                }
            } else {
                echo '<option value="">No hay tipos disponibles</option>';
            }
            $stmt_tipos->close();
            ?>
        </select>
        
        <div style="margin-top: 15px;">
            <button type="submit" class="btn">Guardar</button>
            <a href="productos.php" class="btn" style="background:#666;margin-left:10px;text-decoration:none;display:inline-block;">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
