<?php
// Vérifier la méthode de la requête
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données envoyées via POST
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Charger les données du fichier JSON
    $cptData = json_decode(file_get_contents('cpt.json'), true);

    // Parcourir les données pour trouver une correspondance avec l'email fourni
    foreach ($cptData as $cpt) {
        if ($cpt['email'] === $email) {
            // Si un compte correspondant est trouvé, vérifiez le mot de passe
            if (password_verify($password, $cpt['mdp'])) {
                // Les informations d'identification sont correctes
                echo "Connexion réussie !";
                exit; // Arrêtez l'exécution du script après avoir envoyé la réponse
            } else {
                // Le mot de passe est incorrect
                echo "Adresse e-mail ou mot de passe incorrect.";
                exit;
            }
        }
    }
    // Si aucun compte correspondant n'est trouvé
    echo "Adresse e-mail ou mot de passe incorrect.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* Styles pour centrer les éléments */
        html, body {
            background-color: #a8e0ff;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .login-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .input-group {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bcryptjs/2.4.3/bcrypt.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/Projet/ajax.js"></script>
</head>

<body>
<div class="login-container">
<div class="input-group">
        <h1>Connexion</h1>
        <label for="email">E-mail :</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="button" id="login-btn">Se connecter</button>
        <div>    </div><br>
        <button type="button" id="create-account-btn" onclick="VersCCpt()">Créer un compte</button>
    </div>
</div>    
</body>



<script>
$(document).ready(function() {
    // Écouter le clic sur le bouton de connexion
    $("#login-btn").click(function() {
        // Récupérer les valeurs des champs d'email et de mot de passe
        var email = $("#email").val();
        var password = $("#password").val();
        
        // Envoyer les données d'identification au script PHP pour vérification
        $.ajax({
            type: "POST",
            url: "connexion.php",
            data: { email: email, password: password },
            success: function(response) {
                
                // Vérifier si la réponse indique une connexion réussie
                if (response.trim() === "Connexion réussie !") {
                    // Redirection vers createScrutin.php après une connexion réussie
                    window.location.href = "createScrutin.php?email=" + encodeURIComponent(email);
                } else {
                    // Afficher un message d'erreur si la connexion échoue
                    alert("Erreur: " + response);
                }
            },
            error: function(error) {
                // En cas d'erreur, afficher un message d'erreur
                alert("Erreur: " + error.responseText);
                window.location.href = "connexion.php";
            }
        });
    });
});


    function VersCCpt() {
        window.location.href = "createCpt.php";
    }
</script>

</html>
