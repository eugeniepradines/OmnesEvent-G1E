$(document).ready(function () {
    var mapElement = document.getElementById('event-map');
    if (!mapElement || typeof L === 'undefined') {
        if (mapElement) {
            mapElement.classList.add('map-unavailable');
            mapElement.textContent = 'Carte indisponible pour le moment.';
        }
        return;
    }

    var lat = parseFloat(mapElement.dataset.lat);
    var lng = parseFloat(mapElement.dataset.lng);
    if (Number.isNaN(lat) || Number.isNaN(lng)) {
        return;
    }

    var map = L.map(mapElement, { scrollWheelZoom: false }).setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng])
        .addTo(map)
        .bindPopup('<strong>' + $('<div>').text(mapElement.dataset.title || '').html() + '</strong><br>' + $('<div>').text(mapElement.dataset.address || '').html())
        .openPopup();
});
