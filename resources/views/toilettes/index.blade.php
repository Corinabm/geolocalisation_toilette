<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Géolocalisation de Toilettes Publiques</title>
    <style>
        /* Styles pour rendre la carte en plein écran */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        #map {
            height: 100vh; /* 100% de la hauteur du viewport */
            width: 100vw;  /* 100% de la largeur du viewport */
        }

        #panel {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,.3);
            z-index: 10;
        }
    </style>
</head>
<body>

    <div id="map"></div>
    <div id="panel">
        <strong>Distance et Durée</strong>
        <div id="distance"></div>
        <div id="duration"></div>
    </div>

    <script>
        let map;
        let currentPosition;
        let directionsService;
        let directionsRenderer;

        function initMap() {
            // Position par défaut : Fort-de-France, Martinique
            const defaultPosition = { lat: 14.60, lng: -61.07 };

            // Crée la carte
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: defaultPosition,
            });

            // Initialise les services de directions
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

            // Tente de récupérer la position de l'utilisateur
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentPosition = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(currentPosition);

                        new google.maps.Marker({
                            position: currentPosition,
                            map: map,
                            title: "Votre position",
                            icon: {
                                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                            }
                        });
                        loadToilettes(currentPosition);
                    },
                    () => {
                        handleLocationError(true, map);
                        loadAllToilettes();
                    }
                );
            } else {
                handleLocationError(false, map);
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

        function loadToilettes(position) {
            fetch(`/api/toilettes-proches?lat=${position.lat}&lng=${position.lng}`)
                .then(response => response.json())
                .then(data => {
                    displayToilettes(data);
                })
                .catch(error => console.error('Erreur lors de la récupération des données:', error));
        }

        function loadAllToilettes() {
            fetch('/api/toilettes')
                .then(response => response.json())
                .then(data => {
                    displayToilettes(data);
                })
                .catch(error => console.error('Erreur lors de la récupération des données:', error));
        }

        function displayToilettes(toilettes) {
            toilettes.forEach(toilette => {
                const localisation = toilette.localisation;
                if (localisation) {
                    const latLng = { lat: localisation.latitude, lng: localisation.longitude };
                    let iconUrl;
                    if (toilette.etat === 'ouvert') {
                        iconUrl = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                    } else {
                        iconUrl = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
                    }

                    const marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: toilette.nom,
                        icon: {
                            url: iconUrl
                        }
                    });

                    const contentString =
                        `<div>
                            <h3>${toilette.nom}</h3>
                            <p><strong>Adresse:</strong> ${localisation.adresse}</p>
                            <p><strong>Horaires:</strong> ${toilette.horaires || 'Non spécifié'}</p>
                            <p><strong>État:</strong> ${toilette.etat === 'ouvert' ? '🟢 Ouvert' : '🔴 Fermé'}</p>
                            <button onclick="displayRoute(currentPosition, {lat: ${localisation.latitude}, lng: ${localisation.longitude}})">Afficher l'itinéraire</button>
                        </div>`;

                    const infowindow = new google.maps.InfoWindow({
                        content: contentString,
                    });

                    marker.addListener('click', () => {
                        infowindow.open({
                            anchor: marker,
                            map,
                        });
                    });
                }
            });
        }

        // Nouvelle fonction pour afficher l'itinéraire et la distance
        function displayRoute(origin, destination) {
            if (!origin) {
                alert("Votre position n'est pas disponible pour calculer l'itinéraire.");
                return;
            }

            directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: 'WALKING'
            }, (response, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(response);

                    const route = response.routes[0].legs[0];
                    document.getElementById('distance').innerHTML = `Distance : ${route.distance.text}`;
                    document.getElementById('duration').innerHTML = `Durée : ${route.duration.text}`;
                } else {
                    window.alert('Impossible de trouver un itinéraire. Erreur : ' + status);
                }
            });
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap&libraries=routes"></script>
</body>
</html>