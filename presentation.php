<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /**couleur du fond : bleu */
        html, body {
            background-color: #a8e0ff;
            margin: 0;
            padding: 0;
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100%;
        }
        .menu {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pr img {
            width: 100%;
            height: auto;
            display: block;
        }
        .pr h1 {
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
        }

    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/Projet/ajax.js"></script>
</head>
<body>
<div class = "menu">
<div class = "pr">
    <img src="vote.png" >
    <h1> Bienvenue </h1><br><br>
    <div> C'est un système de vote en ligne </div><br>
    <div> Les votes sont encryptés pour rester anonymes </div><br>
    <button onclick="ChoixEtConnex('voter')">Je veux voter pour un scrutin</button><br><br>
    <div>OU</div><br>
    <button onclick="ChoixEtConnex('creer')">Je veux créer un nouveau scrutin</button>
    <button onclick="ChoixEtConnex('gerer')">Je veux gérer un scrutin existant</button><br><br>
</div>
</div>

<script>

</script>


</body>
