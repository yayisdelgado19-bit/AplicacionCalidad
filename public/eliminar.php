<?php

$path = realpath(__DIR__ . '/../config/db.php');

if ($path === false) {
    http_response_code(500);
    exit('Error: No se encontró el archivo de configuración.');
}

require_once $path; // Aquí se carga $conexion (MYSQLi)

// -----------------------------------------------
// 1. Obtener ID de forma segura
// -----------------------------------------------
$id_raw = filter_input(INPUT_GET, 'identificación', FILTER_SANITIZE_NUMBER_INT);

if ($id_raw === null || $id_raw === false || $id_raw === '') {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// Validar entero positivo
$id = filter_var(
    $id_raw,
    FILTER_VALIDATE_INT,
    [
        'options' => [
            'min_range' => 1,
            'max_range' => 2147483647
        ]
    ]
);

if ($id === false) {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// -----------------------------------------------
// 2. ELIMINAR REGISTRO usando MYSQLi PREPARED
// -----------------------------------------------
try {

    $stmt = $conexion->prepare("DELETE FROM persona WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php?status=ok", true, 303);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    exit("Error en la base de datos.");
}


