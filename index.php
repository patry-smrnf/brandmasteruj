<?php

    function wylicz_efektywnosc_glo($ilosc_godzin, $ilosc_sprzedazy)
    {
        $ilosc_akcji = (float)$ilosc_godzin/4;

        return (float)$ilosc_akcji/(float)$ilosc_akcji;
    }

    require_once('scripts/php/web_functions.php');
    require_once('scripts/php/security.php');

    $status = is_logged();
    if(!$status['logged'])
    {
        header("Location: login.php");
        exit();
    }

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        if(isset($_POST['submit']))
        {
            $data = [
                "id_akcji" => $_POST['id_akcji'],
                "ilosc_sprzedazy" => $_POST['ilosc_sprzedazy'],
                "typ_sprzedazy" => "glo",
                "action" => $_POST['submit'] 
            ];
            $response = sendPost("http://localhost/brandmasteruj_v2/api/user/save_sell.php", $data, $_COOKIE['auth_token']);
            $response_decoded = json_decode($response, true);
            if(!empty($response_decoded['Error']))
            {
                $error = $response_decoded['Error'];
            }
            header("Location: ".$_SERVER['PHP_SELF']."?message=" . urlencode($error));
            exit();
        }
    }

    if(isset($_GET['message']))
    {
        $error = $_GET['message'];
    }

    $year = date('Y');
    $month = date('m');
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    $my_month = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_month.php?month=$month&year=$year");
    $my_sales = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_sales.php");

    $sprzedaze_w_miesiac = count($my_sales['sprzedaze']);

    $dzis_miejsce = "Brak";
    $dzis_akcja_id = 0;
    $jutro_miejsce = "Brak";
    $jutro_start = 0;
    $jutro_koniec = 0;

    $suma_godzin_przed_dzisiaj = 0;
    $suma_godzin_po = 0;


    foreach($my_month['akcje'] as $dni)
    {
        if($dni['date'] === $today) //szukanie co dzisiaj
        {
            if($dni['miejsce'] !== null) //jesli nie jest null
            {
                $id_sklepu = $dni['miejsce'];
                $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_sklepu");
                $dzis_miejsce = $shop_data['adres'];
                $dzis_akcja_id = $dni['id_akcji'];
            }
        }
        if($dni['date'] === $tomorrow) //szukanie co jutro
        {
            if($dni['miejsce'] !== null) //jesli nie jest null
            {
                $id_sklepu = $dni['miejsce'];
                $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_sklepu");
                $jutro_miejsce = $shop_data['adres'];
                $jutro_start = $dni['start'];
                $jutro_koniec = $dni['koniec'];
            }
        }

        //sumowanie godzin
        if (strtotime($dni['date']) < strtotime($today)) //tylko dni po dzisiaj
        {
            //$suma_godzin_przed_dzisiaj += $record['koniec_godzina'] - $record['start_godzina'];
            $start = new DateTime($dni['start']);
            $end = new DateTime($dni['koniec']);
            $diff = $start->diff($end);
            
            // Convert the difference to total hours
            $totalHours = $diff->h + ($diff->i / 60) + ($diff->s / 3600);
            
            $suma_godzin_przed_dzisiaj += $totalHours;
        }
        if (strtotime($dni['date']) >= strtotime($today)) //tylko dni po dzisiaj
        {
            $start = new DateTime($dni['start']);
            $end = new DateTime($dni['koniec']);
            $diff = $start->diff($end);
            
            // Convert the difference to total hours
            $totalHours = $diff->h + ($diff->i / 60) + ($diff->s / 3600);
            
            $suma_godzin_po += $totalHours;            
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
                            <h1><?php echo $dzis_miejsce; ?></h1>
                        </div>
                        <h1>Jak tobie poszlo?</h1>
                        <form method="POST" action="index.php">
                            <div class="field padding-bottom--24">
                                <input type="text" name="ilosc_sprzedazy">
                            </div>
                            <input type="hidden" name="id_akcji" value="<?php echo $dzis_akcja_id; ?>">
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Dodaj">
                            </div>
                        </form>
                        <?php
                            echo $error;
                        ?>
                    </div>
                    <div class="content">
                        <h1>Twoj punkt na jutro:</h1>
                        <div class="calendar_data_box">
                            <h2>Punkt</h2>
                            <h1><?php echo $jutro_miejsce; ?></h1>
                            <h2>Godziny</h2>
                            <h1><?php echo $jutro_start . ' - '. $jutro_koniec; ?></h1>
                            <h2>Srednia sprzedaz</h2>
                            <h1>16-21</h1>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="content">
                        <h1>Ilosc przepracowanych godzin w tym miesiacu</h1>
                        <a class="text_important"><?php echo intval($suma_godzin_przed_dzisiaj); ?></a>
                        <h1>Twoje sprzedaze w tym miesiacu</h1>
                        <a class="text_important"><?php echo intval($sprzedaze_w_miesiac); ?></a>
                        <hr class="solid">
                        <h1>Ilosc zadeklerowanych godzin</h1>
                        <a class="text_important"><?php echo intval($suma_godzin_przed_dzisiaj + $suma_godzin_po); ?></a>
                        <h1>Twoja efektywnosc</h1>
                        <a class="text_important"><?php echo (float)wylicz_efektywnosc_glo(intval($suma_godzin_przed_dzisiaj), $sprzedaze_w_miesiac); ?></a>
                    </div>
                    <div class="content">
                        <h1>Dodaj / Zmien  ( Sprzedaze ):</h1>
                        <a class="basic_button_a" href="calendar_editor.php"> 
                            Kliknij tutaj
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>