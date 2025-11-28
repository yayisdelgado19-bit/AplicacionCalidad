<?php
session_start();
require_once "../config/db.php";

if($_POST){
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    // Convertimos la contraseña a MD5 porque así está en tu tabla
    $pass_md5 = md5($pass);

    // Consulta correcta según tu tabla
    $query = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND clave = ?");
    $query->bind_param("ss", $user, $pass_md5);
    $query->execute();
    $resultado = $query->get_result();

    if($resultado->num_rows == 1){
        $_SESSION['login'] = true;
        header("Location: productos.php");
        exit();
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/style.css">
<title>Login</title>
</head>
<body>
<div class="form-container">
<h2>Iniciar sesión</h2>
<form method="POST">
<input type="text" name="user" placeholder="Usuario">
<input type="password" name="pass" placeholder="Contraseña">
<button class="btn" type="submit">Entrar</button>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</form>
</div>
</body>
</html>
