document.addEventListener('DOMContentLoaded', function () {
    var mapElement = document.getElementById('event-map');
    if (!mapElement) {
        return;
    }

    function setUnavailable(message) {
        mapElement.classList.add('map-unavailable');
        mapElement.textContent = message || 'Carte indisponible pour le moment.';
    }

    if (typeof L === 'undefined') {
        setUnavailable('Carte indisponible pour le moment.');
        return;
    }

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.textContent = value || '';
        return div.innerHTML;
    }

    function drawMap(lat, lng) {
        mapElement.classList.remove('map-unavailable');
        mapElement.textContent = '';

        var map = L.map(mapElement, { scrollWheelZoom: false }).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([lat, lng])
            .addTo(map)
            .bindPopup('<strong>' + escapeHtml(mapElement.dataset.title) + '</strong><br>' + escapeHtml(mapElement.dataset.address))
            .openPopup();
    }

    var lat = parseFloat(mapElement.dataset.lat);
    var lng = parseFloat(mapElement.dataset.lng);
    if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
        drawMap(lat, lng);
        return;
    }

    var address = (mapElement.dataset.address || '').trim();
    if (!address || typeof fetch === 'undefined') {
        setUnavailable('Adresse affichee sans carte : coordonnees introuvables.');
        return;
    }

    mapElement.classList.add('map-unavailable');
    mapElement.textContent = 'Chargement de la carte...';

    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address), {
        headers: { 'Accept': 'application/json' }
    })
        .then(function (response) { return response.ok ? response.json() : []; })
        .then(function (results) {
            if (!results.length || !results[0].lat || !results[0].lon) {
                setUnavailable('Adresse affichee sans carte : coordonnees introuvables.');
                return;
            }
            drawMap(parseFloat(results[0].lat), parseFloat(results[0].lon));
        })
        .catch(function () {
            setUnavailable('Adresse affichee sans carte : coordonnees introuvables.');
        });
});
