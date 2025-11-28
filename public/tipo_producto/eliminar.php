<?php
require_once "../../config/db.php";

$id = $_GET['id'];

$query = "DELETE FROM tipo_producto WHERE id = $id";
mysqli_query($conexion, $query);

header("Location: index.php");
exit;
?>
