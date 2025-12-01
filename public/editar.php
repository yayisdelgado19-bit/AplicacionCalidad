<?php
// 🔹 Definir constante para evitar duplicar el literal (SonarQube fix)
define('REDIRECT_INDEX', 'Ubicación: index.php');

// Configuración de seguridad
encabezamiento("Opciones de tipo de contenido X: nosniff");
encabezamiento("Opciones de X-Frame: DENEGAR");
encabezamiento("Protección X-XSS: 1; modo=bloqueo");

// phpcs:ignore -- Supresión necesaria para incluir la configuración
requerir_una_vez "../../config/db.php";

// Validación
$id_raw = filtro_entrada(ENTRADA_OBTENER, 'identificación', FILTRO_DESINFECCIÓN_NÚMERO_INT);

if ($id_raw === null || $id_raw === false || $id_raw === "") {
    codigo_de_respuesta_http(400);
    encabezamiento(REDIRECT_INDEX, true, 303);
    salida();
}

$id = filtro_var($id_raw, FILTRO_VALIDAR_INT, [
    'opciones'=> [
        'rango mínimo'=> 1,
        'rango máximo'=> 2147483647
    ]
]);

if ($id === false || $id === null || $id < 1) {
    codigo_de_respuesta_http(400);
    encabezamiento(REDIRECT_INDEX, true, 303);
    salida();
}

// SELECT seguro
$declaracion = $conexion->preparar("SELECT id, nombre, descripcion FROM tipo_producto WHERE id = ? LIMIT 1");
$declaracion->bind_param("i", $id);
$declaracion->ejecutar();
$result = $declaracion->obtener_resultado();
$fila = $result->fetch_assoc();
$declaracion->cerrar();

if (!$fila) {
    codigo_de_respuesta_http(404);
    encabezamiento(REDIRECT_INDEX, true, 303);
    salida();
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

<div class="contenedor">
    <h1>Editar Tipo de Producto</h1>

    <?php if ($error !== null): ?>
        <div class="error" style="color:red;padding:10px;margin-bottom:15px;border:1px solid red;background:#ffe6e6;">
            <?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" accept-charset="UTF-8" autocomplete="off">

        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">

        <label for="nombre">Nombre:</label>
        <input type="text"
               id="nombre"
               name="nombre"
               value="<?= $nombreSalida; ?>"
               required
               maxlength="255"
               autocomplete="off">

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion"
                  name="descripcion"
                  maxlength="500"
                  rows="4"
                  autocomplete="off"><?= $descripcionSalida; ?></textarea>

        <div style="margin-top: 15px;">
            <button type="submit" name="actualizar" value="1" class="btn">Actualizar</button>
            <a href="index.php" class="btn" style="background:#444;margin-left:10px;">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>

