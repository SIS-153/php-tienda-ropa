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

// Obtener ID del usuario a eliminar
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: usuarios.php');
    exit();
}

// Evitar eliminar el usuario actual
if ($id == $_SESSION['usuario']['id']) {
    $_SESSION['error'] = 'No puedes eliminarte a ti mismo.';
    header('Location: usuarios.php');
    exit();
}

// Intentar eliminar usuario
if ($usuarioController->eliminarUsuario($id)) {
    $_SESSION['success'] = 'Usuario eliminado exitosamente.';
} else {
    $_SESSION['error'] = 'Hubo un error al eliminar el usuario.';
}

header('Location: usuarios.php');
exit();