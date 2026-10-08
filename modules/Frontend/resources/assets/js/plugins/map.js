import 'leaflet/dist/leaflet.css';
import 'leaflet/dist/leaflet';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
((
    ($) => {
        const mapId = $('#map');
        console.log(1)

        let map = '';
        if (mapId.length) {
            let activeLat = mapId.data('lat');
            let activeLng = mapId.data('lng');
            map = L.map('map').setView([activeLat, activeLng], 16);
            let markerGroup = L.layerGroup().addTo(map);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const customIcon = L.icon({
                iconUrl: markerIcon,
                iconRetinaUrl: markerIcon2x,
                shadowUrl: markerShadow,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            const marker = L.marker([mapId.data('lat'), mapId.data('lng')], {icon: customIcon});
            markerGroup.addLayer(marker);

            const googleMapUrl = `https://www.google.com/maps/dir/?api=1&origin=Current+Location&destination=${mapId.data('lat')},${mapId.data('lng')}`;
            let content = `<a href='${googleMapUrl}' title="${window.translations.get_direction}" target="_blank" rel="noopener noreferrer" class="map-popup">
                                <div class="btn btn-primary map-popup-btn">${window.translations.get_direction}</div></a>`;
            marker.bindPopup(content);
        }
    })
(jQuery));
