<?php
// Configuración de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// phpcs:ignore -- Supresión necesaria para include de configuración
require_once "../../config/db.php";

// ✅ Validación segura del ID sin acceso directo a $_GET
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

// ✅ Prepared statement para DELETE (previene SQL Injection)
$stmt = $conexion->prepare("DELETE FROM tipo_producto WHERE id = ? LIMIT 1");

if (!$stmt) {
    error_log("Error preparando DELETE: " . $conexion->error);
    http_response_code(500);
    exit("Error del sistema");
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // Eliminación exitosa
        $stmt->close();
        $conexion->close();
        header("Location: index.php", true, 303);
        exit();
    } else {
        // No se encontró el registro
        error_log("No se eliminó ningún registro con ID: " . $id);
        $stmt->close();
        $conexion->close();
        header("Location: index.php?error=not_found", true, 303);
        exit();
    }
} else {
    // Error en la ejecución
    error_log("Error ejecutando DELETE: " . $stmt->error);
    $stmt->close();
    $conexion->close();
    header("Location: index.php?error=delete_failed", true, 303);
    exit();
}
?>
