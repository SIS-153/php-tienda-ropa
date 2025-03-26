<?php
session_start();
require_once 'tienda_ropa.php';

// Verificar inicio de sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$database = new Database($host, $db, $user, $pass, $charset);
$stockController = new StockRopaController($database);
$prendas = $stockController->obtenerPrendas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock de Tienda de Ropa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Stock de Ropa</h1>
        
        <!-- Menú de navegación -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Stock</a>
                <?php if ($_SESSION['usuario']['rol'] == 'admin'): ?>
                    <a class="nav-item nav-link" href="usuarios.php">Usuarios</a>
                <?php endif; ?>
                <a class="nav-item nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </nav>

        <!-- Tabla de Stock -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prendas as $prenda): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prenda['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prenda['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($prenda['talla']); ?></td>
                    <td><?php echo htmlspecialchars($prenda['color']); ?></td>
                    <td>$<?php echo number_format($prenda['precio'], 2); ?></td>
                    <td><?php echo $prenda['stock']; ?></td>
                    <td>
                        <a href="editar_prenda.php?id=<?php echo $prenda['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_prenda.php?id=<?php echo $prenda['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botón para agregar nueva prenda -->
        <?php if ($_SESSION['usuario']['rol'] != 'consulta'): ?>
        <a href="agregar_prenda.php" class="btn btn-primary">Agregar Nueva Prenda</a>
        <?php endif; ?>
    </div>
</body>
</html>