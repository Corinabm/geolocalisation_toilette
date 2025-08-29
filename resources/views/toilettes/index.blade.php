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

            // Crée la carte avec une position par défaut
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

                        new google.maps.marker.AdvancedMarkerElement({
                            position: currentPosition,
                            map: map,
                            title: "Votre position",
                            content: new google.maps.marker.PinElement({
                                background: 'blue', // Couleur pour la position de l'utilisateur
                                borderColor: 'white',
                                glyphColor: 'white'
                            }).element
                        });

                        loadToilettes(currentPosition);
                    },
                    (error) => {
                        handleLocationError(error);
                        loadAllToilettes();
                    }
                );
            } else {
                handleLocationError(null);
                loadAllToilettes();
            }
        }

        function handleLocationError(error) {
            let message = '';
            if (error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = "L'accès à la géolocalisation a été refusé.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = "Les informations de localisation ne sont pas disponibles.";
                        break;
                    case error.TIMEOUT:
                        message = "La demande de géolocalisation a expiré.";
                        break;
                }
            } else {
                message = "Votre navigateur ne supporte pas la géolocalisation.";
            }
            console.error(message);
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
                    
                    let iconColor;
                    if (toilette.etat === 'ouvert') {
                        iconColor = 'green';
                    } else {
                        iconColor = 'red';
                    }

                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        map: map,
                        position: latLng,
                        title: toilette.nom,
                        content: new google.maps.marker.PinElement({
                            background: iconColor,
                            borderColor: 'white',
                            glyphColor: 'white'
                        }).element
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
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap&libraries=routes,marker&loading=async"></script>
</body>
</html>