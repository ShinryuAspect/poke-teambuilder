<?php
require_once 'config.php';

class Database {
    private static ?PDO $instancia = null;

    private function __construct() {}

    public static function conectar(): PDO {
        if (self::$instancia === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                
                $opciones = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$instancia = new PDO($dsn, DB_USER, DB_PASS, $opciones);
                
            } catch (PDOException $e) {
                die("Error crítico de conexión en la Base de Datos: " . $e->getMessage());
            }
        }

        return self::$instancia;
    }
}
