<?php
// Vérifier la méthode de la requête
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Charger les données envoyées via POST
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    // Hasher le mot de passe
    $hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);

    // Construire le tableau de données du compte
    $cptData = array(
        "nom" => $nom,
        "prenom" => $prenom,
        "email" => $email,
        "mdp" => $hashedPassword
    );

    // Convertir les données en format JSON
    $jsonData = json_encode($cptData);

    // Vérifier si le fichier existe déjà
    if (file_exists('cpt.json')) {
        // Charger les données existantes du fichier
        $existingData = file_get_contents('cpt.json');
        // Décoder les données JSON existantes en tableau PHP
        $existingAccounts = json_decode($existingData, true);
        // Ajouter le nouveau compte au tableau existant
        $existingAccounts[] = $cptData;
        // Convertir le tableau de comptes en format JSON
        $jsonData = json_encode($existingAccounts, JSON_PRETTY_PRINT);
    } else {
        // S'il n'existe pas, créer un tableau contenant uniquement le nouveau compte
        $jsonData = json_encode(array($cptData), JSON_PRETTY_PRINT);
    }
    

    // Stocker les données dans le fichier cpt.json
    file_put_contents('cpt.json', $jsonData);

    // Répondre à la requête AJAX avec succès
    echo "Compte créé avec succès.";
} else {
    // Répondre à la requête AJAX avec une erreur si la méthode n'est pas POST
    echo "Méthode non autorisée.";
}
?>
