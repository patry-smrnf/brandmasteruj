<?php
    include('scripts/php/security.php');
    include('scripts/php/web_functions.php');

    $status = is_logged();

    if(!$status['logged'])
    {
        header("Location: login.php");
        exit();
    }

    $addresses = fetchGet("http://localhost/brandmasteruj_newBackend/api/server/shop_for_map.php");
?>


<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/basic.css" rel="stylesheet">
        <link href="css/form_styles.css" rel="stylesheet">
        <link href="css/calendar.css" rel="stylesheet">
        <link href="css/mapka.css" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    </head>
    <body>
        <div class="header_top">
            <a href="index.php">
                <button class="header_button active_button">
                    <img src="images/home-icon-silhouette.svg" alt="Location">
                </button>
            </a>
            <a href="calendar.php">
                <button class="header_button">
                    <img src="images/calendar-symbol.svg" alt="Location">
                </button>
            </a>
            <a href="map.php">
                <button class="header_button">
                    <img src="images/map-pin.svg" alt="Location">
                </button>
            </a>
        </div>
        <div class="container">
            <div class="main_body">
                <div id="map" class="mapka"></div>
                <script>
                    var map = L.map('map').setView([52.23758174766404,21.020029089624405], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);


                    var locations = <?php echo json_encode($addresses); ?>;
                    locations.forEach(function (loc) {
                    var popupContent = `
                        <div>
                            <strong>${loc.adres}</strong><br>
                            <a href="map_editor.php?id_miejsca=${loc.id_sklepu}" 
                            target="_blank" 
                            style="display:inline-block;margin-top:5px;padding:5px 10px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;">
                                Dodaj do grafiku
                            </a>
                        </div>
                    `;
                    L.marker([loc.lat, loc.lon])
                        .addTo(map)
                        .bindPopup(popupContent);
                });
                </script>
            </div>
        </div>

    </body>
</html>