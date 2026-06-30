<?php
class SmogonService {
    public function buscarEspecie(string $query): array {
        $pokemonDataset = [
            'Bulbasaur', 'Ivysaur', 'Venusaur', 'Charmander', 'Charmeleon', 'Charizard',
            'Squirtle', 'Wartortle', 'Blastoise', 'Pikachu', 'Dragapult', 'Dragonite',
            'Gholdengo', 'Garchomp', 'Great Tusk', 'Kingambit', 'Roaring Moon', 'Iron Valiant',
            'Ting-Lu', 'Chien-Pao', 'Chi-Yu', 'Wo-Chien', 'Ogerpon', 'Ursaluna'
        ];

        $resultados = [];
        $queryMinuscula = strtolower($query);

        foreach ($pokemonDataset as $pokemon) {
            if (str_contains(strtolower($pokemon), $queryMinuscula)) {
                $resultados[] = $pokemon;
            }
        }

        return array_slice($resultados, 0, 5);
    }
}
