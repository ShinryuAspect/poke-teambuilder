<?php
require_once __DIR__ . '/../../config/database.php';

class Equipo {
    private PDO $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function guardar(int $idUsuario, string $nombreEquipo, string $formatoTier, array $pokemonList): bool {
        try {
            $this->db->beginTransaction();

            $sqlEquipo = "INSERT INTO equipos (id_usuario, nombre_equipo, formato_tier) VALUES (?, ?, ?)";
            $stmtEquipo = $this->db->prepare($sqlEquipo);
            $stmtEquipo->execute([$idUsuario, $nombreEquipo, $formatoTier]);
            
            $idEquipo = $this->db->lastInsertId();

            $sqlPokemon = "INSERT INTO pokemon (
                id_equipo, pokemon_name, objeto, habilidad, naturaleza, 
                movimiento_1, movimiento_2, movimiento_3, movimiento_4, 
                evs_reparticion, ivs_reparticion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtPkmn = $this->db->prepare($sqlPokemon);

            foreach ($pokemonList as $pkmn) {
                if (empty($pkmn['pokemon_name'])) continue;

                $stmtPkmn->execute([
                    $idEquipo,
                    htmlspecialchars(trim($pkmn['pokemon_name'])),
                    htmlspecialchars(trim($pkmn['objeto'] ?? '')),
                    htmlspecialchars(trim($pkmn['habilidad'] ?? '')),
                    htmlspecialchars(trim($pkmn['naturaleza'] ?? '')),
                    htmlspecialchars(trim($pkmn['movimiento_1'] ?? '')),
                    htmlspecialchars(trim($pkmn['movimiento_2'] ?? '')),
                    htmlspecialchars(trim($pkmn['movimiento_3'] ?? '')),
                    htmlspecialchars(trim($pkmn['movimiento_4'] ?? '')),
                    htmlspecialchars(trim($pkmn['evs_reparticion'] ?? '')),
                    htmlspecialchars(trim($pkmn['ivs_reparticion'] ?? ''))
                ]);
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function obtenerPorUsuario(int $idUsuario): array {
        $sql = "SELECT id, nombre_equipo, formato_tier, fecha_creacion 
                FROM equipos 
                WHERE id_usuario = ? 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll();
    }

    public function eliminar(int $idEquipo, int $idUsuario): bool {
        $sql = "DELETE FROM equipos WHERE id = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idEquipo, $idUsuario]);
    }
}
