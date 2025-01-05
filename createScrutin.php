
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de Scrutin</title>
    <!--<script src="/chemin/vers/jsencrypt.js"></script>--><!-- Inclure JSEncrypt.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script><!--Inclure JSEncrypt en dehors de téléchargement-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/Projet/ajax.js"></script>
    <style>
        body {
            background-color: #a8e0ff;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: grid;
            grid-template-columns: 1fr 1fr; /* Diviser la page en deux colonnes */
        }

        #createS #vote{
            height: 100vh; 
            width: 50%; /* 50% de la largeur de la fenêtre */
            box-sizing: border-box;
        }

        #control-panel {
            height: 100vh; 
            background-color: #f0f0f0;
            width: 50%; /* 50% de la largeur de la fenêtre */
            justify-self: end; /* Aligner le panneau de contrôle à droite */
            box-sizing: border-box; /* Pour inclure les paddings et les bordures dans la largeur */
        }

        label,
        input,
        textarea,
        ul,
        p,
        button {
            margin-left: 2em; 
        }

        h1,
        h2 {
            margin-left: 1em;
        }
    </style>
</head>
<body>
    <div id="createS">
    <h1>Création de scrutin</h1>
        <label for="Orga"> Organisateur : </label>
        <!-- Récupérer l'e-mail de l'utilisateur à partir de l'URL -->
        <?php
        // Vérifier si l'e-mail est présent dans l'URL
        if (isset($_GET['email'])) {
            // Échapper les caractères spéciaux pour des raisons de sécurité
            $organisateur = htmlspecialchars($_GET['email']);
            // Afficher l'e-mail dans la case d'organisateur
            echo '<input type="text" id="Orga" name="Orga" value="' . $organisateur . '" required><br><br>';
        } 
        ?>
        <label for="Q"> Question : </label><br>
        <textarea id="Q" name="Q" rows="8" cols="100" required></textarea><br><br>

        <label for="options">Options :</label>
                <input type="text" id="option" name="option">
                <button type="button" onclick="addOption()"> + </button> 
        <ul id="options-list">
                <!-- Les options ajoutées seront affichées ici -->
        </ul>

        <label for="electeur">Électeur :</label>
        <!-- Ajout de l'attribut pattern pour vérifier le format d'un e-mail -->
        <input type="text" id="ele" name="ele"pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Veuillez entrer une adresse e-mail valide" required>
        <button type="button" onclick="addElecteur()"> + </button>
        <ul id="electeurs-list">
                <!-- Les électeurs ajoutées seront affichées ici -->
        </ul>

        <label for="proxy">Procuration :</label>
        <input type="checkbox" id="proxy" onchange="Procuration()"><br><br>
        <div id="procurationFields" style="display: none;">
        <input type="text" id="people1" required>
        <label for="procure"> procure à </label>
        <input type="text" id="people2" required>
        <button type="button" onclick="addProcuration()"> + </button> 
        <ul id="procurations-list">
        </ul>
        </div>
        <br><br>
        <button type="button" id="createBtn" onclick="createScrutin()" disabled>Créer le scrutin</button>
        <button type="button" id ="gererBtn" onclick="gererScrutin()" disabled> Gérer le scrutin</button><br><br>
        <button typr="button" onclick="partir()"> Partir </button>
    </div>

    <div id="vote">
        <h2> Je vote </h2><br><br>
        <label id="elector"> Électeur : </label><br>
        <?php
        // Vérifier si l'e-mail est présent dans l'URL
        if (isset($_GET['email'])) {
            // Échapper les caractères spéciaux pour des raisons de sécurité
            $elector = htmlspecialchars($_GET['email']);
            // Afficher l'e-mail dans la case d'organisateur
            echo '<input type="text" id="electorInput" name="electorInput" value="' . $elector . '" required><br><br>';
        } 
        ?>
        <label id="ask"> Question : </label><br><br>
        <label id="choisir"> Choisissez une option et cliquez "Voter" ! </label><br>
        <div id="options"></div><br><br>
        <div id="nbElecteurs"></div><br><br>
        <button type="button" id="valider-btn" onclick="validerVote()"> Valider </button>
        <button typr="button" onclick="partir()"> Partir </button>
    </div>

    <div id="control-panel">
        <!-- Ajoutez le contenu du panneau de contrôle ici -->
        <!-- Par exemple, une case pour insérer le code du scrutin -->
        <br><br>
        <label for="scrutin-code">Code du scrutin :</label><br>
        <input type="text" id="scrutin-code" name="scrutin-code" disabled><br>
        <button type="button" id="scrutin-try" onclick="tryScrutinCode()" disabled>ElecteurEntrer</button>
        <button type="button" id="tryScrutinOrga" onclick="tryScrutinOrga()" disabled>OrganisateurEntrer</button><br><br>
        <button type="button" id="check" onclick="check()"> RegarderResultat </button><br>
        <button id="closeScrutin" disabled>Clôture du scrutin</button><br>
    </div>


    <script>
        function addOption() {
            var optionInput = document.getElementById("option");
            var optionValue = optionInput.value.trim();
            var optionsList = document.getElementById("options-list");
            if (optionValue !== "") {
                var li = document.createElement("li");
                var textNode = document.createTextNode(optionValue);
                li.appendChild(textNode);

                // Créer un bouton de suppression pour cette option
                var deleteBtn = document.createElement("button");
                deleteBtn.textContent = "-";
                deleteBtn.type = "button";
                deleteBtn.onclick = function() {
                    optionsList.removeChild(li);
                };
                li.appendChild(deleteBtn);

                optionsList.appendChild(li);
                optionInput.value = "";
            } else if (optionsList.childElementCount === 0) {
                alert("Veuillez ajouter au moins une option.");
            }
        }
        
        function addElecteur(){
            var eleInput = document.getElementById("ele");
            var eleValue = eleInput.value.trim();
            var eleList = document.getElementById("electeurs-list");
            // Vérifier si la valeur saisie correspond à un e-mail valide
            // Vérifier si la valeur saisie correspond à un e-mail valide
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (emailPattern.test(eleValue)) {
                var li = document.createElement("li");
                li.textContent = eleValue;

                // Créer un bouton de suppression pour cet électeur
                var deleteBtn = document.createElement("button");
                deleteBtn.textContent = "-";
                deleteBtn.type = "button";
                deleteBtn.onclick = function() {
                    eleList.removeChild(li);
                };
                li.appendChild(deleteBtn);

                eleList.appendChild(li);
                eleInput.value = "";
            } else{
                alert("Veuillez entrer une adresse e-mail valide.");
            }
        }

        function addProcuration(){
            var people1 = document.getElementById("people1");
            var people2 = document.getElementById("people2");
            var people1Value = people1.value.trim();
            var people2Value = people2.value.trim();
            
            // Vérifier si la valeur saisie correspond à un e-mail valide
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (emailPattern.test(people1Value) && emailPattern.test(people2Value)) {
                var procurationText = people1Value + " procure à " + people2Value;
                
                console.log("procurationText : ", procurationText);

                var procurationList = document.getElementById("procurations-list");
                var li = document.createElement("li");
                var textNode = document.createTextNode(procurationText);
                li.appendChild(textNode);

                // Créer un bouton de suppression pour cette procuration
                var deleteBtn = document.createElement("button");
                deleteBtn.textContent = "-";
                deleteBtn.type = "button";
                deleteBtn.onclick = function() {
                    procurationList.removeChild(li);
                };
                li.appendChild(deleteBtn);

                // Ajouter l'élément de liste à la liste de procuration
                procurationList.appendChild(li);

                // Effacer les champs après l'ajout de la procuration
                people1.value = "";
                people2.value = "";
            } else{
                alert("Veuillez entrer une adresse e-mail valide.");
                return ;
            }
        }

        // Lorsque l'utilisateur clique sur le bouton "Entrer" 
        //pour accéder page voter à un scrutin
        /**Partie voter !!! */
        function tryScrutinCode() {
            // Récupérer la valeur du champ du code du scrutin et usr connceté.
            var scrutinCode = document.getElementById("scrutin-code").value;
            localStorage.setItem('scrutinCode', scrutinCode);
            const usrConnecte = document.getElementById("electorInput").value;

            // Charger les données du fichier scrutin.json
            fetch('scrutin.json')
                .then(response => response.json())
                .then(data => {
                    // Rechercher le scrutin correspondant au code saisi par l'utilisateur
                    const scrutin = data.find(scrutin => scrutin.code === scrutinCode);
                    /** 
                    //test
                    console.log('Longueur du code entrée :', scrutinCode.length);
                    console.log('Longueur du code scrutin trouvé :', scrutinCodeTrouve.length);
                    console.log('Code scrutin trouvé :', scrutinCodeTrouve);
                    console.log('Code entrée :', scrutinCode);
                    */
                    // Vérifier si le code du scrutin correspond à celui saisi par l'utilisateur
                    if (scrutin) {
                        // Vérifier si l'utilisateur connecté est autorisé à voter pour ce scrutin
                        const electeur = scrutin.electeurs.find(electeur => electeur.email === usrConnecte);
                        if(electeur){
                            // Afficher le nombre d'électeurs autorisés dans l'interface
                            const nbElecteursAutorises = electeur.nb;
                            document.getElementById('nbElecteurs').textContent = "Nombre vote autorisés : " + nbElecteursAutorises;

                            // Afficher la question derrière l'élément avec l'ID "ask"
                            document.getElementById('ask').textContent = "Question : " + scrutin.question;

                            // Récupérer le conteneur pour les options
                            const optionsContainer = document.getElementById('options');
                            optionsContainer.innerHTML = ""; // Effacer le contenu précédent
                            scrutin.options.forEach(option => {
                                const radioBtn = document.createElement('input');
                                radioBtn.type = 'radio';
                                radioBtn.name = 'choix';
                                radioBtn.value = option;
                                optionsContainer.appendChild(radioBtn);

                                const label = document.createElement('label');
                                label.textContent = option;
                                optionsContainer.appendChild(label);

                                optionsContainer.appendChild(document.createElement('br')); // Ajouter un saut de ligne entre chaque option
                            });
                            return;
                        } else {
                            alert("Vous n'êtes pas autorisé à voter pour ce scrutin.");
                            return;
                        } 
                    }
                    alert("Aucun scrutin trouvé pour ce code.");
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données :', error);
                });
        }

        /**Partie gérer !!! */
        function tryScrutinOrga(){
            var scrutinCode = document.getElementById("scrutin-code").value;
            localStorage.setItem('scrutinCode', scrutinCode);
            const usrConnecte = document.getElementById("electorInput").value;
            // Charger les données du fichier scrutin.json
            fetch('scrutin.json')
                .then(response => response.json())
                .then(data => {
                // Parcourir tous les scrutins
                for (let i = 0; i < data.length; i++) {
 
                    const scrutinData = data[i];
                    var scrutinCodeTrouve = scrutinData.code;
                    // Vérifier si le code du scrutin correspond à celui saisi par l'utilisateur
                    if (scrutinCodeTrouve.trim() === scrutinCode.trim()) {
                        const orgaAutorises =scrutinData.organisateur;
                        if(orgaAutorises == usrConnecte){
                            // Afficher les données du scrutin dans les champs correspondants
                            $("#Orga").val(scrutinData.organisateur);
                            $("#Q").val(scrutinData.question);
                            $("#options-list").empty();
                            scrutinData.options.forEach(option => {
                                var optionItem = $("<li>").text(option);
                                var deleteOptionBtn = $("<button>").text("-");
                                deleteOptionBtn.click(function() {
                                    $(this).parent().remove();
                                });
                                optionItem.append(deleteOptionBtn);
                                $("#options-list").append(optionItem);
                            
                                $("#options-list").append(document.createElement("br"));
                            });
                            $("#electeurs-list").empty();
                            scrutinData.electeurs.forEach(electeur => {
                                var electeurItem = $("<li>").text(electeur.email);

                                var deleteElecteurBtn = $("<button>").text("-");
                                deleteElecteurBtn.click(function() {
                                    $(this).parent().remove();
                                });
                                electeurItem.append(deleteElecteurBtn);
                                $("#electeurs-list").append(electeurItem);

                                $("#electeurs-list").append(document.createElement("br"));
                            });
                            //如果显示了之前的procuration，那么这个就会让gererScrutin()
                            //再次运行检查mandant是否存在和retirer之类的，就会产生错误。
                            // Afficher les procurations si elles existent
                            if (scrutinData.procurations) {
                                $("#procurations-list").empty();
                                scrutinData.procurations.forEach(procuration => {
                                    var procurationItem = $("<li>").text(procuration.mandant + " procure à " + procuration.mandataire);
                                    var deleteProcurationBtn = $("<button>").text("-");
                                    deleteProcurationBtn.click(function() {
                                        $(this).parent().remove();
                                    });
                                    procurationItem.append(deleteProcurationBtn);
                                    $("#procurations-list").append(procurationItem);
                                    $("#procurations-list").append(document.createElement("br"));
                                });
                            } else {
                                // Si aucune procuration n'existe, afficher les informations sur le mandant et le mandataire
                                $("#procurations-list").empty();
                                scrutinData.electeurs.forEach(electeur => {
                                    if (electeur.mandant && electeur.mandataire) {
                                        var procurationItem = $("<li>").text(electeur.mandant + " procure à " + electeur.mandataire);
                                        $("#procurations-list").append(procurationItem);
                                        $("#procurations-list").append(document.createElement("br"));
                                    }
                                });
                            }
                    

                            return;
                        } else {
                            console.log("usr: " + usrConnecte);
                            console.log("orgaAutorises: " + orgaAutorises);
                            alert("Vous n'êtes pas organisateur de ce scrutin.");
                            return;
                        } 
                    }
                }
                alert("Aucun scrutin trouvé pour ce code.");
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données :', error);
            });
        }
        
        function Procuration(){
            var checkBox = document.getElementById("proxy");
            var procurationFields = document.getElementById("procurationFields");
            if (checkBox.checked == true){
                procurationFields.style.display = "block";
            } else {
                procurationFields.style.display = "none";
            }
        }

        function partir(){
            window.location.href = "presentation.php";
        }

        function check(){
            var scrutinCode = document.getElementById("scrutin-code").value;
            localStorage.setItem('scrutinCode', scrutinCode);
            const usrConnecte = document.getElementById("electorInput").value;
            fetch('scrutin.json')
                .then(response => response.json())
                .then(data => {
                // Parcourir tous les scrutins
                for (let i = 0; i < data.length; i++) {
                    
                    const scrutinData = data[i];
                    var scrutinCodeTrouve = scrutinData.code;

                    if (scrutinCodeTrouve.trim() === scrutinCode.trim()) {
                        const orgaAutorises =scrutinData.organisateur;
                        const scrutinClosed = scrutinData.closed;
                        const electeurs = scrutinData.electeurs;
                        // Vérifier si l'utilisateur est dans la liste des électeurs du scrutin et si le scrutin est fermé
                        const userInElectors = electeurs.some(electeur => electeur.email === usrConnecte);
                        if (scrutinClosed && userInElectors) {
                            window.location.href = "result.php";
                        }else if(orgaAutorises == usrConnecte){
                            //Si usr est son organisateur, alors il peut l'accéder n'importe quand
                            window.location.href = "result.php";
                        }else{
                            alert("Vous n'êtes pas autorisé.")
                            return;
                        }
                    }else{
                        alert("Le scrutin n'existe pas.")
                        return;
                    }
                }
            });
        }


// Fonction pour activer ou désactiver la case du code du scrutin
    $(document).ready(function() {

        var choice = localStorage.getItem('choice');
        console.log("choice reçue: ", choice);
        var scrutinCodeInput = $("#scrutin-code");
        var scrutinTryBtn = $("#scrutin-try");
        var tryOrgaBtn = $("#tryScrutinOrga");
        var createBtn = $("#createBtn");
        var gererBtn = $("#gererBtn");
        var closeBtn = $("#closeScrutin");

        if (choice === "gerer") {
            gererBtn.prop("disabled", false);
            createBtn.prop("disabled", true);
            scrutinCodeInput.prop("disabled", false);
            scrutinTryBtn.prop("disabled", true);
            tryOrgaBtn.prop("disabled", false);
            closeBtn.prop("disabled", false);
            console.log("enabled");
            document.getElementById('createS').style.display = 'block';
            document.getElementById('vote').style.display = 'none';
        } else if (choice === "voter"){
            gererBtn.prop("disabled", true);
            createBtn.prop("disabled", true);
            scrutinCodeInput.prop("disabled", false);
            scrutinTryBtn.prop("disabled", false);
            tryOrgaBtn.prop("disabled", true);
            closeBtn.prop("disabled", true);
            document.getElementById('vote').style.display = 'block';
            document.getElementById('createS').style.display = 'none';
        } else if (choice === "creer"){
            gererBtn.prop("disabled", true);
            createBtn.prop("disabled", false);
            scrutinCodeInput.prop("disabled", true);
            scrutinTryBtn.prop("disabled", true);
            tryOrgaBtn.prop("disabled", true);
            closeBtn.prop("disabled", true);
            document.getElementById('createS').style.display = 'block';
            document.getElementById('vote').style.display = 'none';
        }
    });


        // Fonction pour clore le scrutin
        $("#closeScrutin").click(function() {
        $.ajax({
            url: "closeScrutin.php",
            type: "POST",
            success: function(response) {
                alert("Le scrutin a été clos avec succès.");
                // Redirection vers une autre page ou mise à jour de l'interface utilisateur
                window.location.href = "result.php";
            },
            error: function(xhr, status, error) {
                alert("Une erreur s'est produite lors de la fermeture du scrutin : " + error);
            }
        });
    });



</script>
    
</body>
</html>

