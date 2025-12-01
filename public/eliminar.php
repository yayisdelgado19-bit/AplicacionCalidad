<?php

$path = realpath(__DIR__ . '/../config/db.php');

if ($path === false) {
    http_response_code(500);
    exit('Error: No se encontró el archivo de configuración.');
}
require_once $path;
$id_raw = filter_input(INPUT_GET, 'identificación', FILTER_SANITIZE_NUMBER_INT);

if ($id_raw === null || $id_raw === false || $id_raw === '') {
    http_response_code(400);
    header("Location: index.php", true, 303);
    exit();
}
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
try {
    $stmt = $pdo->prepare("DELETE FROM tu_tabla WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php?status=ok", true, 303);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    exit("Error en la base de datos.");
}

