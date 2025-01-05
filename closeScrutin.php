<?php
// Charger les données actuelles du fichier scrutin.json
$scrutinData = json_decode(file_get_contents("scrutin.json"), true);

// Parcourir les scrutins pour trouver celui qui doit être clos (peut-être basé sur un paramètre passé dans la requête POST)
foreach ($scrutinData as &$scrutin) {
    // Ajouter un champ "closed" avec la valeur true pour indiquer que le scrutin est clos
    $scrutin['closed'] = true;
}

// Enregistrer les modifications dans scrutin.json
file_put_contents("scrutin.json", json_encode($scrutinData));

// Répondre à la requête AJAX avec succès
echo "Le scrutin a été clos avec succès.";
?>
