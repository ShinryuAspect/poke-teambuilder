<?php
require_once __DIR__ . '/../../config/database.php';

class Equipo {
    private PDO $db;
    private ?array $pokemonColumns = null;

    public function __construct() {
        $this->db = Database::conectar();
        $this->asegurarColumnasOpcionalesPokemon();
    }

    private function asegurarColumnasOpcionalesPokemon(): void {
        try {
            $columnas = $this->obtenerColumnasPokemon();

            if (!in_array('gender', $columnas, true)) {
                $this->db->exec("ALTER TABLE pokemon ADD COLUMN gender VARCHAR(1) NOT NULL DEFAULT 'N' AFTER ivs_reparticion");
                $this->pokemonColumns = null;
            }

            $columnas = $this->obtenerColumnasPokemon();
            if (!in_array('shiny', $columnas, true)) {
                $this->db->exec("ALTER TABLE pokemon ADD COLUMN shiny TINYINT(1) NOT NULL DEFAULT 0 AFTER gender");
                $this->pokemonColumns = null;
            }
        } catch (PDOException $e) {
            $this->pokemonColumns = null;
        }
    }

    private function obtenerColumnasPokemon(): array {
        if ($this->pokemonColumns !== null) {
            return $this->pokemonColumns;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM pokemon");
        $this->pokemonColumns = array_map(static fn(array $columna) => $columna['Field'], $stmt->fetchAll());

        return $this->pokemonColumns;
    }

    private function prepararDatosPokemon(array $pkmn): array {
        $datos = [
            'pokemon_name'    => htmlspecialchars(trim($pkmn['pokemon_name'] ?? '')),
            'objeto'          => htmlspecialchars(trim($pkmn['objeto'] ?? '')),
            'habilidad'       => htmlspecialchars(trim($pkmn['habilidad'] ?? '')),
            'naturaleza'      => htmlspecialchars(trim($pkmn['naturaleza'] ?? '')),
            'movimiento_1'    => htmlspecialchars(trim($pkmn['movimiento_1'] ?? '')),
            'movimiento_2'    => htmlspecialchars(trim($pkmn['movimiento_2'] ?? '')),
            'movimiento_3'    => htmlspecialchars(trim($pkmn['movimiento_3'] ?? '')),
            'movimiento_4'    => htmlspecialchars(trim($pkmn['movimiento_4'] ?? '')),
            'evs_reparticion' => htmlspecialchars(trim($pkmn['evs_reparticion'] ?? '')),
            'ivs_reparticion' => htmlspecialchars(trim($pkmn['ivs_reparticion'] ?? '')),
        ];

        $columnas = $this->obtenerColumnasPokemon();

        if (in_array('gender', $columnas, true)) {
            $gender = strtoupper(trim((string)($pkmn['gender'] ?? 'N')));
            if (!in_array($gender, ['N', 'M', 'F'], true)) {
                $gender = 'N';
            }
            $datos['gender'] = $gender;
        }

        if (in_array('shiny', $columnas, true)) {
            $datos['shiny'] = !empty($pkmn['shiny']) ? 1 : 0;
        }

        return $datos;
    }

    public function guardar(int $idUsuario, string $nombreEquipo, array $pokemonList): bool {
        try {
            $this->db->beginTransaction();

            $sqlEquipo = "INSERT INTO equipos (id_usuario, nombre_equipo, formato_tier) VALUES (?, ?, ?)";
            $stmtEquipo = $this->db->prepare($sqlEquipo);
            $stmtEquipo->execute([$idUsuario, $nombreEquipo, '']);
            
            $idEquipo = $this->db->lastInsertId();

            $sqlPokemon = "INSERT INTO pokemon (
                id_equipo, pokemon_name, objeto, habilidad, naturaleza, 
                movimiento_1, movimiento_2, movimiento_3, movimiento_4, 
                evs_reparticion, ivs_reparticion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            foreach ($pokemonList as $pkmn) {
                if (empty($pkmn['pokemon_name'])) continue;

                $datosPokemon = $this->prepararDatosPokemon($pkmn);
                $columnas = array_keys($datosPokemon);
                $placeholders = implode(', ', array_fill(0, count($columnas) + 1, '?'));
                $sql = sprintf(
                    'INSERT INTO pokemon (id_equipo, %s) VALUES (%s)',
                    implode(', ', $columnas),
                    $placeholders
                );
                $stmtPkmn = $this->db->prepare($sql);
                $stmtPkmn->execute(array_merge([$idEquipo], array_values($datosPokemon)));
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function actualizar(int $idEquipo, int $idUsuario, string $nombreEquipo, array $pokemonList): bool {
        try {
            $this->db->beginTransaction();

            $sqlActualizarEquipo = "UPDATE equipos SET nombre_equipo = ?, formato_tier = ? WHERE id = ? AND id_usuario = ?";
            $stmtEquipo = $this->db->prepare($sqlActualizarEquipo);
            $stmtEquipo->execute([$nombreEquipo, '', $idEquipo, $idUsuario]);

            $sqlBorrarViejos = "DELETE FROM pokemon WHERE id_equipo = ?";
            $stmtBorrar = $this->db->prepare($sqlBorrarViejos);
            $stmtBorrar->execute([$idEquipo]);

            $sqlPokemon = "INSERT INTO pokemon (
                id_equipo, pokemon_name, objeto, habilidad, naturaleza, 
                movimiento_1, movimiento_2, movimiento_3, movimiento_4, 
                evs_reparticion, ivs_reparticion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            foreach ($pokemonList as $pkmn) {
                if (empty($pkmn['pokemon_name'])) continue;

                $datosPokemon = $this->prepararDatosPokemon($pkmn);
                $columnas = array_keys($datosPokemon);
                $placeholders = implode(', ', array_fill(0, count($columnas) + 1, '?'));
                $sql = sprintf(
                    'INSERT INTO pokemon (id_equipo, %s) VALUES (%s)',
                    implode(', ', $columnas),
                    $placeholders
                );
                $stmtPkmn = $this->db->prepare($sql);
                $stmtPkmn->execute(array_merge([$idEquipo], array_values($datosPokemon)));
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function obtenerPorUsuario(int $idUsuario): array {
        $sql = "SELECT id, nombre_equipo, fecha_creacion 
                FROM equipos 
                WHERE id_usuario = ? 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll();
    }

    public function obtenerDetallesEquipo(int $idEquipo, int $idUsuario): array {
        $sqlEquipo = "SELECT id, nombre_equipo FROM equipos WHERE id = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($sqlEquipo);
        $stmt->execute([$idEquipo, $idUsuario]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            return [];
        }

        $sqlPokemon = "SELECT * FROM pokemon WHERE id_equipo = ?";
        $stmtPkmn = $this->db->prepare($sqlPokemon);
        $stmtPkmn->execute([$idEquipo]);
        $pokemons = $stmtPkmn->fetchAll(PDO::FETCH_ASSOC);

        return [
            'info' => $equipo,
            'pokemon' => $pokemons
        ];
    }

    public function eliminar(int $idEquipo, int $idUsuario): bool {
        $sql = "DELETE FROM equipos WHERE id = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idEquipo, $idUsuario]);
    }
}
