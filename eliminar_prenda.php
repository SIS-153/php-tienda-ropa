<?php
session_start();
require_once 'tienda_ropa.php';

// Verify login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Verify deletion permissions
if ($_SESSION['usuario']['rol'] == 'consulta') {
    die('No tienes permisos para eliminar prendas');
}

$database = new Database($host, $db, $user, $pass, $charset);
$stockController = new StockRopaController($database);

// Get the ID of the clothing item to delete
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Try to delete the clothing item
if ($stockController->eliminarPrenda($id)) {
    header('Location: index.php?mensaje=Prenda eliminada exitosamente');
    exit();
} else {
    die('Error al eliminar la prenda');
}