<?php
// Vérifier la méthode de la requête
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Charger les données envoyées via POST
    $jsonData = json_decode(file_get_contents("php://input"), true);
    
    // Vérifier si le fichier existe déjà
    if (file_exists('scrutin.json')) {
        // Charger les données existantes du fichier
        $existingData = file_get_contents('scrutin.json');
        // Si des données existent déjà, ajouter une virgule après la dernière ligne
        $existingData = rtrim($existingData, "]") . ",\n";
        // Ajouter les nouvelles données
        $existingData .= $jsonData . "]";
    } else {
        // S'il n'existe pas, écrire les nouvelles données entre des crochets
        $existingData = "[" . $jsonData . "]";
    }

    // Si les données ne sont pas pour un compte, les stocker dans scrutin.json
    file_put_contents("scrutin.json", json_encode($jsonData, JSON_PRETTY_PRINT));
    // Répondre à la requête AJAX avec succès
    echo "Données du scrutin enregistrées avec succès.";
}
?>