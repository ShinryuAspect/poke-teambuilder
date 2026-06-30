<?php
class SmogonService {
    public function buscarEspecie(string $query): array {
        $queryMinuscula = strtolower(trim($query));
        
        $url = "https://pokeapi.co/api/v2/pokemon?limit=1025";

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
            $nombreOriginal = $pokemon['name'];

            if (str_contains($nombreOriginal, '-gmax') || str_contains($nombreOriginal, '-totem')) {
                continue;
            }
            
            $nombreFormateado = ucwords(str_replace('-', ' ', $nombreOriginal));
            
            if (str_contains(strtolower($nombreFormateado), $queryMinuscula)) {
                $resultados[] = $nombreFormateado;
            }
            
            if (count($resultados) >= 5) {
                break;
            }
        }
        
        return $resultados;
    }
}
