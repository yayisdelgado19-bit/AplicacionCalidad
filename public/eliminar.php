<?php
// Configuración de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// phpcs:ignore -- Supresión necesaria para include de configuración
require_once "../../config/db.php";

// ✅ SOLUCIÓN: Usar filter_input en lugar de $_GET directamente
$id_raw = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Validar que el ID existe y no está vacío
if ($id_raw === null || $id_raw === false || $id_raw === '') {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// Validar que es un número entero positivo
$id = filter_var($id_raw, FILTER_VALIDATE_INT, [
    'options' => [
        'min_range' => 1,
        'max_range' => 2147483647
    ]
]);

// Si la validación falla, redirigir
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// ✅ SOLUCIÓN: Usar prepared statement en lugar de concatenar SQL
$stmt = $conexion->prepare("DELETE FROM tipo_producto WHERE id = ? LIMIT 1");

// Verificar que se preparó correctamente
if (!$stmt) {
    error_log("Error preparando DELETE: " . $conexion->error);
    http_response_code(500);
    exit("Error del sistema");
}

// Vincular el parámetro como entero
$stmt->bind_param("i", $id);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Verificar si se eliminó algún registro
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conexion->close();
        header("Location: index.php", true, 303);
        exit();
    } else {
        // No se encontró el registro
        error_log("Registro no encontrado con ID: " . $id);
        $stmt->close();
        $conexion->close();
        header("Location: index.php", true, 303);
        exit();
    }
} else {
    // Error al ejecutar
    error_log("Error ejecutando DELETE: " . $stmt->error);
    $stmt->close();
    $conexion->close();
    header("Location: index.php", true, 303);
    exit();
}
?>
