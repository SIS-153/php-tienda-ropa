<?php
// Configuración de depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos con verificación de variables de entorno
$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'tienda_ropa';
$DB_USER = getenv('DB_USER') ?: 'tienda_user';
$DB_PASS = getenv('DB_PASS') ?: 'tienda_password';
$DB_CHARSET = 'utf8mb4';

// Función de registro de errores
function logError($message) {
    error_log($message);
    file_put_contents('/var/www/html/detailed_db_error.log', 
        date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 
        FILE_APPEND
    );
}

// Clase de conexión con más depuración
class Database {
    private $conn;

    public function __construct($host, $db, $user, $pass, $charset) {
        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $user, $pass, $options);
            logError("Conexión PDO establecida correctamente");
        } catch (PDOException $e) {
            logError("Error de conexión PDO en constructor: " . $e->getMessage());
            throw $e;
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Función para configurar base de datos
function configurarBaseDeDatos() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;
    
    try {
        $db = new Database($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET);
        $conn = $db->getConnection();

        // Crear tablas
        $conn->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'vendedor', 'consulta') NOT NULL
        )");

        $conn->exec("CREATE TABLE IF NOT EXISTS stock_ropa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            categoria ENUM('camisetas', 'pantalones', 'vestidos', 'chaquetas', 'accesorios') NOT NULL,
            talla VARCHAR(10) NOT NULL,
            color VARCHAR(50) NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL
        )");

        // Verificar si ya existe un usuario admin
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute(['admin@tienda.com']);
        $count = $stmt->fetchColumn();

        // Crear usuario admin solo si no existe
        if ($count == 0) {
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
            $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
            $stmt->execute(['Admin Principal', 'admin@tienda.com', $hashedPassword, 'admin']);
            logError("Usuario admin creado exitosamente");
        } else {
            logError("Usuario admin ya existe, omitiendo creación");
        }

    } catch (Exception $e) {
        logError("Error en configurarBaseDeDatos: " . $e->getMessage());
        throw $e;
    }
}

// Depuración de variables de entorno
logError("Variables de entorno:");
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'DB_') === 0) {
        logError("$key: $value");
    }
}

// Llamada a configuración
try {
    configurarBaseDeDatos();
} catch (Exception $e) {
    logError("Error fatal: " . $e->getMessage());
    die("Error de configuración: " . $e->getMessage());
}
?>