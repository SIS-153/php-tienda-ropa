<?php
session_start();
require_once 'tienda_ropa.php';

// Verificar inicio de sesión y permisos de admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: login.php');
    exit();
}

$database = new Database($host, $db, $user, $pass, $charset);
$usuarioController = new UsuarioController($database);

// Obtener ID del usuario a editar
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: usuarios.php');
    exit();
}

$usuarios = $usuarioController->obtenerUsuarios();
$usuarioEditar = null;
foreach ($usuarios as $usuario) {
    if ($usuario['id'] == $id) {
        $usuarioEditar = $usuario;
        break;
    }
}

if (!$usuarioEditar) {
    header('Location: usuarios.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    if ($usuarioController->actualizarUsuario($id, $nombre, $email, $rol)) {
        header('Location: usuarios.php');
        exit();
    } else {
        $error = 'Hubo un error al actualizar el usuario.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Usuario</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuarioEditar['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuarioEditar['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" class="form-control" required>
                    <option value="admin" <?php echo $usuarioEditar['rol'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="vendedor" <?php echo $usuarioEditar['rol'] == 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                    <option value="consulta" <?php echo $usuarioEditar['rol'] == 'consulta' ? 'selected' : ''; ?>>Consulta</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>