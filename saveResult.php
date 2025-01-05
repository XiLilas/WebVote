<?php
// Vérifier si des données ont été reçues en POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer les données JSON envoyées depuis la page JavaScript
    $jsonData = file_get_contents('php://input');

    // Vérifier si les données JSON sont valides
    $data = json_decode($jsonData, true);

    if ($data !== null) {
        // Charger les données actuelles du fichier resultat.json
        $currentData = file_get_contents('resultat.json');
        $currentVotes = json_decode($currentData, true);

        // Ajouter le nouveau vote à la liste existante
        $currentVotes[] = $data;

        // Enregistrer les données mises à jour dans le fichier resultat.json
        file_put_contents('resultat.json', json_encode($currentVotes, JSON_PRETTY_PRINT));

        // Répondre avec un code HTTP 200 (OK) pour indiquer que l'enregistrement a réussi
        http_response_code(200);
    } else {
        // Répondre avec un code HTTP 400 (Bad Request) si les données JSON sont invalides
        http_response_code(400);
    }

    
} else {
    // Répondre avec un code HTTP 405 (Method Not Allowed) si la méthode de requête n'est pas autorisée
    http_response_code(405);
}
?>
