<?php
// Database configuration
$host = 'db';
$db   = 'tienda_ropa';
$user = 'tienda_user';
$pass = 'tienda_password';
$charset = 'utf8mb4';

// Database connection class
class Database {
    private $conn;

    public function __construct($host, $db, $user, $pass, $charset) {
        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

// User management controller
class UsuarioController {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    // Check if user exists
    public function usuarioExiste($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    // Create new user
    public function crearUsuario($nombre, $email, $password, $rol) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, $hash, $rol]);
    }

    // Create user only if not exists
    public function crearUsuarioSiNoExiste($nombre, $email, $password, $rol) {
        try {
            // Check if user already exists
            if (!$this->usuarioExiste($email)) {
                return $this->crearUsuario($nombre, $email, $password, $rol);
            }
            return false; // User already exists
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    // Read users
    public function obtenerUsuarios() {
        $stmt = $this->db->query("SELECT id, nombre, email, rol FROM usuarios");
        return $stmt->fetchAll();
    }

    // Update user
    public function actualizarUsuario($id, $nombre, $email, $rol) {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
        return $stmt->execute([$nombre, $email, $rol, $id]);
    }

    // Delete user
    public function eliminarUsuario($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Login
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }
}

// Clothing stock management controller
class StockRopaController {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    // Check if clothing item exists
    public function prendaExiste($nombre, $categoria, $talla, $color) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stock_ropa WHERE nombre = ? AND categoria = ? AND talla = ? AND color = ?");
        $stmt->execute([$nombre, $categoria, $talla, $color]);
        return $stmt->fetchColumn() > 0;
    }

    // Create new clothing item
    public function crearPrenda($nombre, $categoria, $talla, $color, $precio, $stock) {
        $stmt = $this->db->prepare("INSERT INTO stock_ropa (nombre, categoria, talla, color, precio, stock) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $categoria, $talla, $color, $precio, $stock]);
    }

    // Create clothing item only if not exists
    public function crearPrendaSiNoExiste($nombre, $categoria, $talla, $color, $precio, $stock) {
        try {
            // Check if clothing item already exists
            if (!$this->prendaExiste($nombre, $categoria, $talla, $color)) {
                return $this->crearPrenda($nombre, $categoria, $talla, $color, $precio, $stock);
            }
            return false; // Clothing item already exists
        } catch (Exception $e) {
            error_log("Error creating clothing item: " . $e->getMessage());
            return false;
        }
    }

    // Read clothing items
    public function obtenerPrendas() {
        $stmt = $this->db->query("SELECT * FROM stock_ropa");
        return $stmt->fetchAll();
    }

    // Update stock
    public function actualizarStock($id, $stock) {
        $stmt = $this->db->prepare("UPDATE stock_ropa SET stock = ? WHERE id = ?");
        return $stmt->execute([$stock, $id]);
    }

    // Update clothing item
    public function actualizarPrenda($id, $nombre, $categoria, $talla, $color, $precio, $stock) {
        $stmt = $this->db->prepare("UPDATE stock_ropa SET nombre = ?, categoria = ?, talla = ?, color = ?, precio = ?, stock = ? WHERE id = ?");
        return $stmt->execute([$nombre, $categoria, $talla, $color, $precio, $stock, $id]);
    }

    // Delete clothing item
    public function eliminarPrenda($id) {
        $stmt = $this->db->prepare("DELETE FROM stock_ropa WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Search clothing items by category
    public function buscarPorCategoria($categoria) {
        $stmt = $this->db->prepare("SELECT * FROM stock_ropa WHERE categoria = ?");
        $stmt->execute([$categoria]);
        return $stmt->fetchAll();
    }

    // New method to get a clothing item by its ID
    public function obtenerPrendaPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM stock_ropa WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // New method to edit a clothing item
    public function editarPrenda($id, $nombre, $categoria, $talla, $color, $precio, $stock) {
        $stmt = $this->db->prepare("UPDATE stock_ropa SET nombre = ?, categoria = ?, talla = ?, color = ?, precio = ?, stock = ? WHERE id = ?");
        return $stmt->execute([$nombre, $categoria, $talla, $color, $precio, $stock, $id]);
    }
}

// Database setup function
function configurarBaseDeDatos() {
    global $host, $db, $user, $pass, $charset;
    
    try {
        $database = new Database($host, $db, $user, $pass, $charset);
        $conn = $database->getConnection();

        // Create users table
        $conn->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'vendedor', 'consulta') NOT NULL
        )");

        // Create clothing stock table
        $conn->exec("CREATE TABLE IF NOT EXISTS stock_ropa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            categoria ENUM('camisetas', 'pantalones', 'vestidos', 'chaquetas', 'accesorios') NOT NULL,
            talla VARCHAR(10) NOT NULL,
            color VARCHAR(50) NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL
        )");

        return $database;
    } catch (Exception $e) {
        error_log("Database configuration error: " . $e->getMessage());
        throw $e;
    }
}

// Usage example
try {
    // Configure database and get instance
    $database = configurarBaseDeDatos();

    // Initialize controllers
    $usuarioController = new UsuarioController($database);
    $stockController = new StockRopaController($database);

    // Create admin user only if not exists
    $usuarioController->crearUsuarioSiNoExiste('Admin Principal', 'admin@tienda.com', 'password123', 'admin');

    // Create sample clothing item only if not exists
    $stockController->crearPrendaSiNoExiste('Basic T-Shirt', 'camisetas', 'M', 'White', 29.99, 50);

} catch (Exception $e) {
    error_log("Configuration error: " . $e->getMessage());
    die("Configuration error: " . $e->getMessage());
}
?>