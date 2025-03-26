<?php
session_start();
require_once 'tienda_ropa.php';

// Verify login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Verify editing permissions
if ($_SESSION['usuario']['rol'] == 'consulta') {
    die('No tienes permisos para editar prendas');
}

$database = new Database($host, $db, $user, $pass, $charset);
$stockController = new StockRopaController($database);

// Get the ID of the clothing item to edit
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If edit form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if ($stockController->editarPrenda($id, $nombre, $categoria, $talla, $color, $precio, $stock)) {
        header('Location: index.php?mensaje=Prenda editada exitosamente');
        exit();
    } else {
        $error = "Error al editar la prenda";
    }
}

// Get clothing item data
$prenda = $stockController->obtenerPrendaPorId($id);

// If no clothing item found, redirect
if (!$prenda) {
    header('Location: index.php?mensaje=Prenda no encontrada');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Prenda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Editar Prenda</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($prenda['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label>Categoría</label>
            <select name="categoria" class="form-control" required>
                <option value="camisetas" <?php echo ($prenda['categoria'] == 'camisetas') ? 'selected' : ''; ?>>Camisetas</option>
                <option value="pantalones" <?php echo ($prenda['categoria'] == 'pantalones') ? 'selected' : ''; ?>>Pantalones</option>
                <option value="vestidos" <?php echo ($prenda['categoria'] == 'vestidos') ? 'selected' : ''; ?>>Vestidos</option>
                <option value="chaquetas" <?php echo ($prenda['categoria'] == 'chaquetas') ? 'selected' : ''; ?>>Chaquetas</option>
                <option value="accesorios" <?php echo ($prenda['categoria'] == 'accesorios') ? 'selected' : ''; ?>>Accesorios</option>
            </select>
        </div>
        <div class="form-group">
            <label>Talla</label>
            <input type="text" name="talla" class="form-control" value="<?php echo htmlspecialchars($prenda['talla']); ?>" required>
        </div>
        <div class="form-group">
            <label>Color</label>
            <input type="text" name="color" class="form-control" value="<?php echo htmlspecialchars($prenda['color']); ?>" required>
        </div>
        <div class="form-group">
            <label>Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $prenda['precio']; ?>" required>
        </div>
        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" value="<?php echo $prenda['stock']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>