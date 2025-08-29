<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Géolocalisation de Toilettes Publiques</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

    <h1>Géolocalisation de Toilettes Publiques</h1>
    <div id="map"></div>

    <script>
        let map;
        let currentPosition;

        function initMap() {
            // Position par défaut : Fort-de-France, Martinique
            const defaultPosition = { lat: 14.60, lng: -61.07 };

            // Crée la carte
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: defaultPosition,
            });

            // Tente de récupérer la position de l'utilisateur
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Fonction de succès : si l'utilisateur autorise l'accès
                        currentPosition = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        // Centre la carte sur la position de l'utilisateur
                        map.setCenter(currentPosition);

                        // Ajoute un marqueur pour l'utilisateur
                        new google.maps.Marker({
                            position: currentPosition,
                            map: map,
                            title: "Votre position",
                            icon: {
                                // Icône par défaut pour la position de l'utilisateur
                                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                            }
                        });

                        // Charge les toilettes les plus proches une fois la position obtenue
                        loadToilettes(currentPosition);
                    },
                    () => {
                        // Fonction d'erreur : si l'utilisateur refuse l'accès
                        handleLocationError(true, map);
                        // Charge toutes les toilettes si l'accès est refusé
                        loadAllToilettes();
                    }
                );
            } else {
                // Le navigateur ne supporte pas la géolocalisation
                handleLocationError(false, map);
                // Charge toutes les toilettes si la fonctionnalité est absente
                loadAllToilettes();
            }
        }

        function handleLocationError(browserHasGeolocation, map) {
            console.error(
                browserHasGeolocation
                    ? "Erreur : L'accès à la géolocalisation a été refusé."
                    : "Erreur : Votre navigateur ne supporte pas la géolocalisation."
            );
        }

        // Fonction pour charger les toilettes les plus proches
        function loadToilettes(position) {
            // Appel de la nouvelle API Laravel avec les coordonnées de l'utilisateur
            fetch(`/api/toilettes-proches?lat=${position.lat}&lng=${position.lng}`)
                .then(response => response.json())
                .then(data => {
                    displayToilettes(data);
                })
                .catch(error => console.error('Erreur lors de la récupération des données:', error));
        }

        // Fonction pour charger toutes les toilettes (en cas d'échec de géolocalisation)
        function loadAllToilettes() {
            fetch('/api/toilettes')
                .then(response => response.json())
                .then(data => {
                    displayToilettes(data);
                })
                .catch(error => console.error('Erreur lors de la récupération des données:', error));
        }

        // Fonction pour afficher les marqueurs
        function displayToilettes(toilettes) {
            toilettes.forEach(toilette => {
                const localisation = toilette.localisation;
                if (localisation) {
                    const latLng = { lat: localisation.latitude, lng: localisation.longitude };

                    // Détermination de l'icône en fonction de l'état
                    let iconUrl;
                    if (toilette.etat === 'ouvert') {
                        iconUrl = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                    } else { // 'ferme'
                        iconUrl = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
                    }

                    const marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: toilette.nom,
                        icon: {
                            url: iconUrl // Attribution de l'icône personnalisée
                        }
                    });

                    // Création d'un contenu pour l'infobulle
                    const contentString =
                        `<div>
                            <h3>${toilette.nom}</h3>
                            <p><strong>Adresse:</strong> ${localisation.adresse}</p>
                            <p><strong>Horaires:</strong> ${toilette.horaires || 'Non spécifié'}</p>
                            <p><strong>État:</strong> ${toilette.etat === 'ouvert' ? '🟢 Ouvert' : '🔴 Fermé'}</p>
                        </div>`;

                    const infowindow = new google.maps.InfoWindow({
                        content: contentString,
                    });

                    // Ajout d'un écouteur d'événement pour afficher l'infobulle au clic
                    marker.addListener('click', () => {
                        infowindow.open({
                            anchor: marker,
                            map,
                        });
                    });
                }
            });
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap"></script>
</body>
</html>