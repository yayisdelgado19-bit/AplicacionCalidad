<?php
// Cargar configuración SIN usar rutas relativas inseguras
require_once __DIR__ . '/../config/db.php';

// ----------------------------------------------------------
// 1. Obtener ID usando filter_input (NO usar $_GET directamente)
// ----------------------------------------------------------
$id_raw = filter_input(INPUT_GET, 'identificación', FILTER_SANITIZE_NUMBER_INT);

// ----------------------------------------------------------
// 2. Validar que el ID exista y no esté vacío
// ----------------------------------------------------------
if ($id_raw === null || $id_raw === false || $id_raw === "") {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// ----------------------------------------------------------
// 3. Validar que el ID sea un número entero positivo
// ----------------------------------------------------------
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

// ----------------------------------------------------------
// 4. Si la validación falla, redirigir
// ----------------------------------------------------------
if ($id === false) {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}

// Ahora $id es SEGURO para usarse en consultas SQL.
// Asegúrate de usar consultas preparadas (PDO o MySQLi).
