<?php

require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function registrar(string $username, string $email, string $password): bool {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (username, email, password_hash, rol) VALUES (?, ?, ?, 'user')";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$username, $email, $passwordHash]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function login(string $usernameOrEmail, string $password): array|bool {
        $sql = "SELECT id, username, email, password_hash, rol FROM usuarios WHERE username = ? OR email = ? LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            unset($usuario['password_hash']);
            return $usuario; 
        }

        return false;
    }
}