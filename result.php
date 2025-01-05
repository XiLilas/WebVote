<?php
// Vérifier si les paramètres nécessaires sont présents dans l'URL
if (isset($_GET['elector']) && isset($_GET['scrutinCode'])) {
    // Charger les données actuelles de scrutin.json
    $scrutinData = json_decode(file_get_contents("scrutin.json"), true);

    //echo $_GET['elector'], $_GET['scrutinCode'];
    // Parcourir les scrutins pour trouver celui correspondant au scrutin en cours
    foreach ($scrutinData as &$scrutin) {
        if ($scrutin['code'] === $_GET['scrutinCode']) {
            // Parcourir les électeurs du scrutin
            foreach ($scrutin['electeurs'] as &$electeur) {
                // Vérifier si l'e-mail de l'électeur correspond à celui passé dans l'URL
                if ($electeur['email'] === $_GET['elector']) {
                    // Décrémenter le nombre d'électeurs ayant voté
                    $electeur['nb']--;
                }
            }
        }
    }

    // Enregistrer les modifications dans scrutin.json
    file_put_contents("scrutin.json", json_encode($scrutinData));

    // Afficher un message de confirmation ou de succès, etc.
    //echo "Nombre d'électeurs mis à jour.";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats du scrutin</title>
    <!-- Ajouter la bibliothèque Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <h1>Résultats du scrutin à présent</h1>
    <canvas id="voteChart" width="800" height="400"></canvas><br>
    <button id="acceuil" onclick="acceuil()">Acceuil</button>

    <script>
    //Récupérer le code entrée par usr et stocké dans local storage
    var scrutinCodeValue = localStorage.getItem('scrutinCode');
    console.log('Code de scrutin récupéré depuis createScrutin.php:', scrutinCodeValue);
    // Charger les données du fichier resultat.json
    fetch('resultat.json')
        .then(response => response.json())
        .then(data => {
            // Filtrer les votes pour obtenir uniquement ceux 
            //correspondant au code de scrutin récupéré
            const filteredVotes = data.filter(vote => vote.scrutinCode === scrutinCodeValue);

            // Compter le nombre de votes pour chaque option
            const voteCounts = {};
            filteredVotes.forEach(vote => {
            const option = vote.option;
            if (!voteCounts[option]) {
                voteCounts[option] = 1;
            } else {
                voteCounts[option]++;
            }
        });

        // Préparer les données pour l'histogramme
        const labels = Object.keys(voteCounts);
        const dataValues = Object.values(voteCounts);

        // Créer un histogramme avec Chart.js
        const ctx = document.getElementById('voteChart').getContext('2d');
        const voteChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Votes',
                    data: dataValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre de votes'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Options'
                        }
                    }
                }
            }
        });
    })
    .catch(error => {
        console.error('Erreur lors du chargement des données :', error);
    });



    function acceuil(){
        window.location.href= "presentation.php";
    }
</script>
