<?php
class SmogonService {
    public function buscarEspecie(string $query): array {
        $queryMinuscula = strtolower(trim($query));
        
        $url = "https://pokeapi.co/api/v2/pokemon?limit=1000";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PokemonTeamBuilderApp/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $respuesta = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$respuesta) {
            return []; 
        }

        $datos = json_decode($respuesta, true);
        if (!$datos || !isset($datos['results'])) {
            return [];
        }

        $resultados = [];
        foreach ($datos['results'] as $pokemon) {
            $nombreConstruido = ucfirst($pokemon['name']);
            
            if (str_contains(strtolower($nombreConstruido), $queryMinuscula)) {
                $resultados[] = $nombreConstruido;
            }
            
            if (count($resultados) >= 5) {
                break;
            }
        }
        
        return $resultados;
    }
}
