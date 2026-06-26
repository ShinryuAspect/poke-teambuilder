CREATE DATABASE IF NOT EXISTS poke_teambuilder DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE poke_teambuilder;

-- 1. Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Equipos
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_equipo VARCHAR(100) NOT NULL,
    formato_tier VARCHAR(50) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipos_usuarios 
        FOREIGN KEY (id_usuario) REFERENCES usuarios (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Tabla de Detalles de Pokémon por Equipo
CREATE TABLE pokemon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    pokemon_name VARCHAR(50) NOT NULL,
    objeto VARCHAR(50) DEFAULT NULL,
    habilidad VARCHAR(50) DEFAULT NULL,
    naturaleza VARCHAR(50) DEFAULT NULL,
    movimiento_1 VARCHAR(50) DEFAULT NULL,
    movimiento_2 VARCHAR(50) DEFAULT NULL,
    movimiento_3 VARCHAR(50) DEFAULT NULL,
    movimiento_4 VARCHAR(50) DEFAULT NULL,
    evs_reparticion VARCHAR(100) DEFAULT NULL,
    ivs_reparticion VARCHAR(100) DEFAULT NULL,
    CONSTRAINT fk_pokemon_equipos 
        FOREIGN KEY (id_equipo) REFERENCES equipos (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Tabla de Caché para el Traductor Gemini IA
CREATE TABLE traducciones_cache (
    hash_ingles CHAR(40) PRIMARY KEY, -- Optimizado para almacenar un hash SHA-1
    texto_ingles TEXT NOT NULL,
    texto_espanol TEXT NOT NULL,
    fecha_guardado DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;