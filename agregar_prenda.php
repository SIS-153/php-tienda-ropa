<?php
session_start();
require_once 'tienda_ropa.php';

// Verificar permisos
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] == 'consulta') {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database($host, $db, $user, $pass, $charset);
    $stockController = new StockRopaController($database);

    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    if ($stockController->crearPrenda($nombre, $categoria, $talla, $color, $precio, $stock)) {
        $success = 'Prenda agregada exitosamente';
    } else {
        $error = 'Error al agregar la prenda';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Prenda - Tienda de Ropa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Agregar Nueva Prenda</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre de la Prenda</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" class="form-control" required>
                    <option value="camisetas">Camisetas</option>
                    <option value="pantalones">Pantalones</option>
                    <option value="vestidos">Vestidos</option>
                    <option value="chaquetas">Chaquetas</option>
                    <option value="accesorios">Accesorios</option>
                </select>
            </div>
            <div class="form-group">
                <label>Talla</label>
                <input type="text" name="talla" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Color</label>
                <input type="text" name="color" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Precio</label>
                <input type="number" step="0.01" name="precio" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Cantidad en Stock</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Prenda</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>