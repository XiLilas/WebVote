<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <style>
        /* Styles pour centrer les éléments */
        html, body {
            background-color: #a8e0ff;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .create-account-container {
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/Projet/ajax.js"></script>
</head>

<body>
<div class="create-account-container">
    <div class="input-group">
        <h1>Créer un compte</h1>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br><br>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br><br>
        <label for="email">E-mail :</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm-password">Confirmer le mot de passe :</label>
        <input type="password" id="confirm-password" name="confirm-password" required><br><br>
        <button type="button" id="create-btn" onclick="createAccount()">Créer</button>
    </div>
</div>

<script>
</script>

</body>
</html>
