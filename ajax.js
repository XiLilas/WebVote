/**utililser ajax pour créer un nouveau fichier json par php*/
function createScrutin(){
    var organisateur = $("#Orga").val();
    var question = $("#Q").val();
    var options = [];
    $("#options-list li").each(function() {
        options.push($(this).text().replace("-", ""));// Supprimer les "-" lors de la sauvegarde
    });
    var electeurs = [];
    $("#electeurs-list li").each(function() {
        var email = $(this).text().replace("-", "");
        var nb = 1;
        electeurs.push({ email: email, nb: nb });
        //console.log("1 : ", electeurs);
    });
    var procurations = [];
    var validationFailed = false;
    $("#procurations-list li").each(function() {
        var text = $(this).text().replace("-", "");
        var mandant, mandataire;
        // Extraire les valeurs de mandant et mandataire du texte
        var matches = text.match(/([^ ]+) procure à ([^ ]+)/);
        //console.log(matches);
        if (matches && matches.length === 3) {
            mandant = matches[1].trim().replace("-", "");
            mandataire = matches[2].trim().replace("-", "");
            //console.log("Mandant:", mandant);
            //console.log("Mandataire:", mandataire);
        }
        //console.log("Mandant:", mandant);
        //console.log("Mandataire:", mandataire);
        
        //Vérifier si le mandant existe dans la liste des électeurs
        var mandantExiste = false;
        var mandantIndex;
        electeurs.forEach(function(electeur, index){
            //console.log("Electeur : ", electeur);
            //console.log("Mandant : ", mandant);
            if(electeur.email.trim() === mandant.trim()){
                //Vérifier si electeur est éligible de donner la procuration
                if(electeur.nb < 1){
                    alert("Le mandant a déjà donné 1 procurations, procuration échouée.");
                    validationFailed = true;
                    return ; 
                }
                //Au cours de la reunion un participant s’en va et laisse procuration à quelqu’un d’autre. 
                //Au vote suivant l’organisateur le retire de la liste et rajoute un +1vote à celui qui porte la procuration.
                mandantExiste = true;
                mandantIndex = index;
                return false; // Exclure l'électeur de la liste
            }
        });
        if (!mandantExiste){
            alert("mandant n'existe pas, procuration failled.")
            validationFailed = true;
            return ;
        }else{
            // Vérifier si le mandataire est déjà dans la liste des électeurs
            var mandataireExiste = false;
            electeurs.forEach(function(electeur) {
                if (electeur.email === mandataire) {
                    // Vérifier si le mandataire a déjà reçu 2 procurations
                    if (electeur.nb >= 3) {
                        alert("Le mandataire a déjà reçu 2 procurations, procuration échouée.");
                        validationFailed = true;
                        return;
                    }
                    // Sinon, pour le mandataire existe déjà, augmenter son compteur nb de 1
                    electeur.nb++;
                    mandataireExiste = true;
                }
            });
            // Si le mandataire n'existe pas encore dans la liste des électeurs, l'ajouter
            if (!mandataireExiste) {
                electeurs.push({ email: mandataire, nb: 1 });
            }
            procurations.push({ mandant: mandant, mandataire: mandataire });
            //console.log("2Mandant:", mandant);
            //console.log("2Mandataire:", mandataire);
        }
        // Retirer le mandant de la liste des électeurs une fois il a procuré
        if (mandantIndex !== undefined) {
            electeurs.splice(mandantIndex, 1);
        }
    });

    //Vérifiez les valeurs des mandants et mandataires
    console.log("Procurations:", procurations);

    //Si les conditions de procuration ne sont pas remplies, arrêtez la fonction
    if(validationFailed){
        return;
    }

    // Vérifier si des électeurs ont été ajoutés
    if (electeurs.length === 0) {
        alert("Veuillez ajouter au moins un électeur.");
        return; // Arrêter l'exécution de la fonction si aucun électeur n'est ajouté
    }
    //Même vérification pour les options
    if (options.length === 0 ) {
        alert("Veuillez ajouter au moins une option.");
        return; // Arrêter l'exécution de la fonction si aucune option n'est ajoutée
    }

    const codeLength = 6;
    let code = '';
    for (let i = 0; i < codeLength; i++) {
        const digit = Math.floor(Math.random() * 10); // Chiffre aléatoire entre 0 et 9
        code += digit.toString();
    }
    fetch('scrutin.json')
        .then(response => response.json())
        .then(data => {
            // Vérifier si le code généré est déjà présent dans les scrutins existants
            const codesExistants = data.map(scrutin => scrutin.code);
            if (codesExistants.includes(code)) {
                // Si le code généré existe déjà, générer un nouveau code et recommencer le processus
                createScrutin();
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des données :', error);
        });

    var scrutinData = {
        organisateur: organisateur,
        question: question,
        options: options,
        electeurs: electeurs,
        code: code,
        procurations: procurations,
        closed: false // Ajouter la variable closed initialisée à false
    };

    // Charger les données existantes du scrutin
    $.getJSON("scrutin.json", function(existingData) {
        // Ajouter les nouvelles données au tableau existant
        existingData.push(scrutinData);
        // Enregistrer les données mises à jour dans le fichier scrutin.json
        $.ajax({
            type: "POST",
            url: "saveScrutin.php",
            contentType: "application/json",
            data: JSON.stringify(existingData),
            success: function(response) {
                console.log(response);
                alert("Scrutin créé avec succès. Votre clé d'invitation publique est : " + code);
                window.location.href = "presentation.php";
            },
            error: function(error) {
                console.log(error);
                alert("Erreur lors de l'enregistrement des données du scrutin.");
            }
        });
    });
}



function ChoixEtConnex(choice) {
    //console.log("choice :", choice);
    // Stockez la valeur de 'choice' dans localStorage
    localStorage.setItem('choice', choice);
    // Redirigez vers la page de connexion
    window.location.href = "/Projet/connexion.php";
}

// Fonction pour valider les champs et créer le compte
function createAccount() {
    // Récupérer les valeurs des champs
    var nom = document.getElementById("nom").value;
    var prenom = document.getElementById("prenom").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm-password").value;

    // Vérifier si les champs sont vides
    if (nom.trim() === '' || prenom.trim() === '' || email.trim() === '' || password.trim() === '' || confirmPassword.trim() === '') {
        alert("Veuillez remplir tous les champs.");
        return;
    }

    // Vérifier si les mots de passe correspondent
    if (password !== confirmPassword) {
        alert("Les mots de passe ne correspondent pas.");
        return;
    }

    /**TODO : Vérifier si l'email existe déjà un compte*/
    //si oui, ajouter fonctionnalité aide à retrouver ou 
    //réinitialiser le mdp

    var cptData = {
        nom: nom,
        prenom: prenom,
        email: email,
        mdp: password
    };

    $.ajax({
        url: 'saveCpt.php',
        method: 'POST',
        data: cptData,
        success: function(response) {
            console.log(response);
            alert("Compte créé avec succès.");
            window.location.href = "connexion.php";
        },
        error: function(error) {
            console.log(error);
            alert("Erreur lors de la création du compte.");
        }
    });
}

function validerVote(){
    const optionVotee = document.querySelector('input[name="choix"]:checked').value;
    const emailElector = document.getElementById('electorInput').value;
    const scrutinCode = document.getElementById('scrutin-code').value;
    const nbAutoElement = document.getElementById('nbElecteurs').textContent;
    const matches = nbAutoElement.match(/\d+/); // Cherche un nombre dans la chaîne de caractères
    const nbAuto = parseInt(matches[0]); // Convertit la chaîne de caractères en nombre entier
    console.log(nbAuto);

    var Valide = true;
    // Vérifier si le scrutin est ouvert
    fetch('scrutin.json')
        .then(response => response.json())
        .then(data => {
            const scrutin = data.find(scrutin => scrutin.code === scrutinCode);
            if (scrutin.closed) {
                alert("Le scrutin est clos. Vous ne pouvez plus voter.");
                Valide = false;
                return;
            }
        }).catch(error => {
            console.error('Erreur lors du chargement des données du scrutin :', error);
            alert("Erreur lors du chargement des données du scrutin.");
        });

    //Vérifier option est bien choisie
    if (optionVotee.length === 0) {
        alert("Veuillez faire un choix.");
        return; // Arrêter l'exécution de la fonction
    }
    //Cela vérifier en même temps l'élibilité d'électeur car un électeur 
    //non éligible ne peut pas avoir la partie d'option pour choisir
    //Vérifier aussi éligiliblité par nombre de votes que usr disponible
    if(nbAuto >= 1 && Valide){
        //pour encrypter les votes
        var crypt = new JSEncrypt({default_key_size: 256 });
        
        var publicKey = crypt.getPublicKey();
        //et pour récupérer la clé privée
        //var privateKey = localStorage.getItem("privateKey");
        //var privateKey = crypt.getPrivateKey();
        var encryptedPeople = crypt.encrypt(emailElector);
        //localStorage.setItem("privateKey", privateKey);
        
        const vote = {
            option: optionVotee,
            elector: encryptedPeople,
            scrutinCode: scrutinCode
        };
    
        // Envoyer les données du vote au serveur pour les enregistrer dans resultat.json
        fetch('saveResult.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(vote) // Envoyer les données sous forme JSON
        })
        .then(response => {
            // Vérifier si l'enregistrement a réussi
            if (response.ok) {
                console.log('Vote enregistré avec succès.');
                window.location.href = 'result.php?elector=' + encodeURIComponent(emailElector) + '&scrutinCode=' + encodeURIComponent(scrutinCode);
            } else {
                console.error('Erreur lors de l\'enregistrement du vote.');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'enregistrement du vote :', error);
        });

    }else {
        console.log(nbAuto);
        alert("Vous ne disponisé plus le chance de vote.");
    }
}




function gererScrutin() {
    // Récupérer les données du scrutin à partir de l'interface utilisateur
    var usrConnecte = $("#Orga").val();
    var question = $("#Q").val();
    var options = [];
    $("#options-list li").each(function() {
        options.push($(this).text().replace("-", ""));
    });
    //var proxy = $("#proxy").prop("checked") ? "Oui" : "Non";

    var code = $("#scrutin-code").val(); // Récupérer le code du scrutin à partir de l'interface utilisateur

    // Charger les données existantes du scrutin depuis le fichier scrutin.json
    $.getJSON("scrutin.json", function(existingData) {
        //console.log("Données existantes du scrutin:", existingData);
        // Rechercher le scrutin à mettre à jour en fonction du code
        var scrutinToUpdate = existingData.find(function(scrutin) {
            return scrutin.code === code;
        });

        var closORnoOrga = false;
        // Vérifier si le scrutin est ouvert ou fermé
        if (scrutinToUpdate.closed) {
            alert("Le scrutin est déjà clos. Vous ne pouvez plus le gérer.");
            closORnoOrga = true;
            return;
        }
        const orgaAutorises = scrutinToUpdate.organisateur;
        //console.log("Organisateur autorisé:", orgaAutorises);
        //console.log("Organisateur connecté:", usrConnecte);
        if(orgaAutorises != usrConnecte){
            alert("Vous n'êtes pas autorisé à gérer ce scrutin car vous n'êtes pas l'organisateur.");
            return; // Arrêter la fonction
        }

        if (scrutinToUpdate && !closORnoOrga) {
            // Mettre à jour les données du scrutin
            scrutinToUpdate.organisateur = usrConnecte;
            scrutinToUpdate.question = question;
            scrutinToUpdate.options = options;

            var newP = {};
            var validationFailed = false;
            var anciennesP = scrutinToUpdate.procurations;
            $("#procurations-list li").each(function() {
                var text = $(this).text().replace("-", "");
                var mandant, mandataire;
                // Extraire les valeurs de mandant et mandataire du texte
                var matches = text.match(/([^ ]+) procure à ([^ ]+)/);
                //console.log(matches);
                if (matches && matches.length === 3) {
                    mandant = matches[1].trim().replace("-", "");
                    mandataire = matches[2].trim().replace("-", "");
                    //console.log("Mandant:", mandant);
                    //console.log("Mandataire:", mandataire);
                }
                //console.log("Mandant:", mandant);
                //console.log("Mandataire:", mandataire);
                // Vérifier si la nouvelle procuration est déjà dans la liste des anciennes procurations
                const nouvelleP = { mandant: mandant, mandataire: mandataire };
                var estInclue = anciennesP.some(function(ancienne) {
                    return ancienne.mandant === nouvelleP.mandant && ancienne.mandataire === nouvelleP.mandataire;
                });
                if (!estInclue ) {
                    // Si la nouvelle procuration n'existe pas déjà, on fait la suite

                    //Vérifier si le mandant existe dans la liste des électeurs
                    var mandantExiste = false;
                    var mandantIndex;
                    electeurs.forEach(function(electeur, index){
                        //console.log("Electeur : ", electeur);
                        //console.log("Mandant : ", mandant);
                        if(electeur.email.trim() === mandant.trim()){
                            //Vérifier si electeur est éligible de donner la procuration
                            if(electeur.nb < 1){
                                alert("Le mandant ne possède plus le droit de vote, procuration échouée.");
                                validationFailed = true;
                                return ; 
                            }
                            //Au cours de la reunion un participant s’en va et laisse procuration à quelqu’un d’autre. 
                            //Au vote suivant l’organisateur le retire de la liste et rajoute un +1vote à celui qui porte la procuration.
                            mandantExiste = true;
                            mandantIndex = index;
                            return false; // Exclure l'électeur de la liste
                        }
                    });
                    if (!mandantExiste){
                        alert("mandant n'existe pas, procuration failled.")
                        validationFailed = true;
                        return ;
                    }else{
                        // Vérifier si le mandataire est déjà dans la liste des électeurs
                        var mandataireExiste = false;
                        electeurs.forEach(function(electeur) {
                            if (electeur.email === mandataire) {
                                // Vérifier si le mandataire a déjà reçu 2 procurations
                                if (electeur.nb >= 3) {
                                    alert("Le mandataire a déjà reçu 2 procurations, procuration échouée.");
                                    validationFailed = true;
                                    return;
                                }
                                // Sinon, pour le mandataire existe déjà, augmenter son compteur nb de 1
                                electeur.nb++;
                                mandataireExiste = true;
                            }
                        });
                        // Si le mandataire n'existe pas encore dans la liste des électeurs, l'ajouter
                        if (!mandataireExiste) {
                            electeurs.push({ email: mandataire, nb: 1 });
                        }
                        newP = { mandant: mandant, mandataire: mandataire };
                        //console.log("2Mandant:", mandant);
                        //console.log("2Mandataire:", mandataire);
                    }
                    // Retirer le mandant de la liste des électeurs une fois il a procuré
                    if (mandantIndex !== undefined) {
                        electeurs.splice(mandantIndex, 1);
                    }
                }
            });
            //Vérifiez les valeurs des mandants et mandataires
            console.log("Procurations:", newP);
            //Si les conditions de procuration ne sont pas remplies, arrêtez la fonction
            if(validationFailed){
                return;
            }
            if(Object.keys(newP).length !== 0){
                scrutinToUpdate.procurations.push(newP);
            }

            var newE = {};
            var anciennesE = scrutinToUpdate.electeurs;
            // Récupérer les électeurs de l'interface utilisateur
            $("#electeurs-list li").each(function() {
                var email = $(this).text().replace("-", "");
                nouveauE = { email: email, nb: 1 };
                var estInclue = anciennesE.some(function(ancienne) {
                    return ancienne.email === nouveauE.email;
                });
                if (!estInclue ) {
                    // Si le nouvel électeur n'existe pas déjà, on le rajoute
                    newE = { email: email, nb: 1 };
                }
            });
            if(Object.keys(newE).length !== 0){
                scrutinToUpdate.electeurs.push(newE);
            }


            // Enregistrer les données mises à jour dans le fichier scrutin.json
            $.ajax({
                type: "POST",
                url: "saveScrutin.php",
                contentType: "application/json",
                data: JSON.stringify(existingData),
                success: function(response) {
                    console.log(response);
                    alert("Données du scrutin mises à jour avec succès.");
                    // Rediriger vers la page de présentation ou toute autre action appropriée
                    window.location.href = "presentation.php";
                },
                error: function(error) {
                    console.log(error);
                    alert("Erreur lors de la mise à jour des données du scrutin.");
                }
            });
        } else {
            alert("Aucun scrutin trouvé pour ce code.");
        }
    });
}
