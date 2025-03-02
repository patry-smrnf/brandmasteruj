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
    $jutro_miejsce = "Brak";
    $jutro_start;
    $jutro_koniec;

    $suma_godzin_przed_dzisiaj = 0;
    $suma_godzin_po = 0;

    foreach($my_month['akcje'] as $dni)
    {
        if($dni['date'] === $today) //szukanie co dzisiaj
        {
            if($dni['miejsce'] !== null) //jesli nie jest null
            {
                $id_sklepu = $dni['miejsce'];
                $shop_data = fetchGet("http://localhost/brandmasteruj_newBackend/api/server/shop_by_id.php?shop_id=$id_sklepu");
                $dzis_miejsce = $shop_data['adres'];
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
                        <div class="calendar_data_box">
                            <h2>Nazwa</h2>
                            <h1>Swietkorzyska 11</h1>
                        </div>
                        <h1>Jak tobie poszlo?</h1>
                        <form action="POST">
                            <div class="field padding-bottom--24">
                                <input type="email" name="email">
                            </div>
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Dodaj">
                            </div>
                        </form>
                    </div>
                    <div class="content">
                        <h1>Twoj punkt na jutro:</h1>
                        <div class="calendar_data_box">
                            <h2>Punkt</h2>
                            <h1>Swietkorzyska 11</h1>
                            <h2>Godziny</h2>
                            <h1>16-21</h1>
                            <h2>Srednia sprzedaz</h2>
                            <h1>16-21</h1>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="content">
                        <h1>Ilosc godzin w tym miesiacu</h1>
                        <a class="text_important">7</a>
                        <h1>Twoje sprzedaze w tym miesiacu</h1>
                        <a class="text_important">11</a>
                        <hr class="solid">
                        <h1>Ilosc zadeklerowanych godzin</h1>
                        <a class="text_important">71</a>
                        <h1>Twoja efektywnosc</h1>
                        <a class="text_important">2.1</a>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>