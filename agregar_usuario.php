<?php
session_start();
require_once 'tienda_ropa.php';

// Verificar inicio de sesión y permisos de admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';
$database = new Database($host, $db, $user, $pass, $charset);
$usuarioController = new UsuarioController($database);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    if ($usuarioController->crearUsuarioSiNoExiste($nombre, $email, $password, $rol)) {
        header('Location: usuarios.php');
        exit();
    } else {
        $error = 'El usuario ya existe o hubo un error al crear el usuario.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Agregar Nuevo Usuario</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="vendedor">Vendedor</option>
                    <option value="consulta">Consulta</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Usuario</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>