<?php
    require_once('scripts/php/web_functions.php');
    require_once('scripts/php/security.php');

    $status = is_logged();
    if(!$status['logged'])
    {
        header("Location: login.php");
        exit();
    }

    $year = date('Y');
    $month = date('m');
    $today = date('Y-m-d');

    $my_month = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_month.php?month=$month&year=$year");

    $dzis_miejsce = "Brak";
    $dzis_sprzedaze_suma = 0;
    $dzis_akcja_id = 0;
    $dzis_start = 0;
    $dzis_koniec = 0;

    $pozostale_dni = [];

    foreach($my_month['akcje'] as $dni)
    {
        if($dni['date'] === $today) //szukanie co dzisiaj
        {
            if($dni['miejsce'] !== null) //jesli nie jest null
            {
                $id_sklepu = $dni['miejsce'];
                $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_sklepu");
                $sprzedaze_w_tym_sklep = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_sales.php?id_sklep=$id_sklepu");
                
                $dzis_sprzedaze_suma = count($sprzedaze_w_tym_sklep['sprzedaze']);
                $dzis_miejsce = $shop_data['adres'];
                $dzis_akcja_id = $dni['id_akcji'];

                $dzis_start = $dni['start'];
                $dzis_koniec = $dni['koniec'];
            }
        }

        if((strtotime($dni['date']) > strtotime($today)))
        {
            if($dni['miejsce'] !== null)
            {
                $id_sklepu = $dni['miejsce'];
                $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_sklepu");
                $sprzedaze_w_tym_sklep = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_sales.php?id_sklep=$id_sklepu");
                
                $pozostale_dni[] = [
                    "adres" => $shop_data['adres'],
                    "start" => $dni['start'],
                    "koniec" => $dni['koniec'],
                    "suma_sell" => count($sprzedaze_w_tym_sklep['sprzedaze'])
                ];
            }
        }

    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/basic.css" rel="stylesheet">
        <link href="css/form_styles.css" rel="stylesheet">
        <link href="css/calendar.css" rel="stylesheet">
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
                <div class="column">
                    <div class="content">
                        <h1>Twoj punkt dzisiaj:</h1>
                        <div class="calendar_data_box data_box_active">
                            <h2>Punkt</h2>
                            <h1><?php echo $dzis_miejsce; ?></h1>
                            <h2>Godziny</h2>
                            <h1><?php echo $dzis_start . ' - ' . $dzis_koniec; ?></h1>
                            <h2>Suma sprzedazy</h2>
                            <h1><?php echo $dzis_sprzedaze_suma; ?></h1>
                        </div>
                    </div>
                    <div class="content">
                        <h1>Dodaj / Zmien  ( Kalendarz ):</h1>
                        <a class="basic_button_a" href="calendar_editor.php"> 
                            Kliknij tutaj
                        </a>
                    </div>
                </div>
                <div class="column">
                    <div class="content">
                        <h1>Reszta miesiaca:</h1>
                        <?php
                        foreach($pozostale_dni as $pozostala_akcja)
                        {
                            echo '                        
                        <div class="calendar_data_box">
                            <h2>Punkt</h2>
                            <h1>'.$pozostala_akcja['adres'].'</h1>
                            <h2>Godziny</h2>
                            <h1>'.$pozostala_akcja['start']. ' - ' . $pozostala_akcja['koniec'] .'</h1>
                            <h2>Srednia sprzedaz</h2>
                            <h1>'.$pozostala_akcja['suma_sell'].'</h1>
                        </div>';
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>