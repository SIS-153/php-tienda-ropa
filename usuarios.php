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
$usuarios = $usuarioController->obtenerUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Gestión de Usuarios</h1>
        
        <!-- Menú de navegación -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Stock</a>
                <a class="nav-item nav-link" href="usuarios.php">Usuarios</a>
                <a class="nav-item nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </nav>

        <!-- Tabla de Usuarios -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botón para agregar nuevo usuario -->
        <a href="agregar_usuario.php" class="btn btn-primary">Agregar Nuevo Usuario</a>
    </div>
</body>
</html>