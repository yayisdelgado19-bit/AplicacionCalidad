<?php
require_once "../../config/db.php";

$query = "SELECT * FROM tipo_producto";
$result = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Producto</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>

<body>

<div class="container">

    <h1>Tipos de Producto</h1>

    <a class="btn" href="agregar.php">Agregar Tipo de Producto</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['descripcion'] ?></td>
                    <td>
                        <a class="btn" href="editar.php?id=<?= $row['id'] ?>">Editar</a>
                        <a class="btn" href="eliminar.php?id=<?= $row['id'] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>