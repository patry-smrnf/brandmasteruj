<?php
    require_once('scripts/php/web_functions.php');
    require_once('scripts/php/security.php');

    $status = is_logged();
    if(!$status['logged'])
    {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        if(isset($_POST['submit']))
        {
            $data = [
                "lokalizacja_punkt" => $_POST['lokalizacja_punkt'],
                "punkt_godzina_start" => $_POST['punkt_godzina_start'],
                "punkt_godzina_koniec" => $_POST['punkt_godzina_koniec'],
                "akcja_data" => $_POST['punkt_data'],
                "akcja" => $_POST['submit']
            ];
            $response = sendPost("http://localhost/brandmasteruj_v2/api/user/save_event.php", $data, $_COOKIE['auth_token']);
        }
        if(isset($_POST['icloud_button']))
        {
            $data = [
                "year_actual" => $_POST['year_actual'],
                "month_actual" => $_POST['month_actual'],
                "akcja" => $_POST['icloud_button']
            ];
            $response = sendPost("http://localhost/brandmasteruj_v2/api/user/save_icloud.php", $data, $_COOKIE['auth_token']);

            $data_decoded = json_decode($response,true);
            $path = $data_decoded['Succcess'];
            if(!empty($path) && $path !== "")
            {
                header("Location: $path");

            }


        }
    }

    $year = date('Y');
    $month = date('m');
    $today = isset($_GET['active_day']) ? $_GET['active_day'] : date('Y-m-d');

    $my_month = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_month.php?month=$month&year=$year");
    $dzis_miejsce = "Brak";
    $dzis_start = 0;
    $dzis_koniec = 0;

    foreach($my_month['akcje'] as $dni)
    {
        if($dni['date'] === $today) //szukanie co dzisiaj
        {
            if($dni['miejsce'] !== null) //jesli nie jest null
            {
                $id_sklepu = $dni['miejsce'];
                $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_sklepu");
                $dzis_miejsce = $shop_data['adres'];
                $dzis_start = $dni['start'];
                $dzis_koniec = $dni['koniec'];
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
        <link href="css/calendar_editor.css" rel="stylesheet">
        <script src="scripts/js/calendar_editor.js"></script>
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
                        <div class="calendar_picked_day_title" id="picked_day_title">
                            <a id="punkt_data"><?php if(isset($_GET['active_day']))
                            {
                                echo "Wybrany przez ciebie punkt z mapy";
                            }
                            else
                            {
                                echo "Dzisiaj";
                            } ?></a>
                        </div>
                        <div class="calendar_picked_day_events">
                            <form method="POST" action="calendar_editor.php">
                                <h2>Punkt: </h2>
                                <div class="autocomplete" style="width:300px;">
                                    <input id="myInput" type="text" name="lokalizacja_punkt" value="<?php echo $dzis_miejsce;?>" placeholder="Adres">
                                  </div>
                                <h2>W Godzinach: </h2>
                                <div class="field padding-bottom--24">
                                    <input type="text" value="<?php echo $dzis_start;?>" name="punkt_godzina_start" id="punkt_godzina_start">
                                    <input type="text" value="<?php echo $dzis_koniec;?>" name="punkt_godzina_koniec" id="punkt_godzina_koniec">
                                </div>
                                <input type="hidden" value="<?php echo $today;?>" name="punkt_data" id="data_input">

                                <div class="field padding-bottom--24">
                                    <input type="submit" name="submit" value="Zmien">
                                </div>
                                <div class="field padding-bottom--24">
                                    <input type="submit" name="submit" value="Usun">
                                </div>
                            </form>
                            <script src="scripts/js/autocomplete_addresses.js"></script>
                        </div>
                    </div>
                    <div class="content">
                        <h2>Zapisz kalendarz do icloud</h2>
                        <a>Tylko dla osob korzystajacych z macbook lub iphone</a><br><br>
                        <form method="POST" action="calendar_editor.php">
                            <div class="field padding-bottom--24">
                                <input type="hidden" name="year_actual" value="<?php echo $year;?>">
                                <input type="hidden" name="month_actual" value="<?php echo $month;?>">
                                <input type="submit" name="icloud_button" value="Zapisz">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="calendar_picker_editor">
                    <ul>
                        <?php
                            //wypisac wszystkie akcje w postaci kalendarza
                            foreach($my_month['akcje'] as $dni)
                            {
                                $miejsce_akcji = "Brak";
                                $start_akcji = 0;
                                $koniec_akcji = 0;

                                if($dni['miejsce'] !== null) //jesli akcja ma przypisane jakies dane do siebie
                                {

                                    $id_sklepu = $dni['miejsce'];
                                    $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_sklepu");

                                    $miejsce_akcji = $shop_data['adres'];
                                    $start_akcji = $dni['start'];
                                    $koniec_akcji = $dni['koniec'];

                                }

                                if($dni['date'] === $today)
                                {
                                    echo '<li href="#top" class="today" id="date_calendar_window" onclick="set_editor(\''. $miejsce_akcji. '\', \''. $start_akcji .'\', \''. $koniec_akcji.'\', \''. $dni['date'] .'\')"><time id="data_akcji" datetime="'.$dni['date'].'">'.date('d', strtotime($dni['date'])).'</time><a id="adres_akcji_tekst">'.$miejsce_akcji.'</a> 
                                    <a id="godziny_akcji_tekst">'.$start_akcji . '-'.$koniec_akcji .'</a></li>';  
                                }
                                else
                                {
                                    echo '<li href="#top" id="date_calendar_window" onclick="set_editor(\''. $miejsce_akcji. '\', \''. $start_akcji .'\', \''. $koniec_akcji.'\', \''. $dni['date'] .'\')"><time id="data_akcji" datetime="'.$dni['date'].'">'.date('d', strtotime($dni['date'])).'</time><a id="adres_akcji_tekst">'.$miejsce_akcji.'</a> 
                                    <a id="godziny_akcji_tekst">'.$start_akcji . '-'.$koniec_akcji .'</a></li>';                              
                                }

                            }

                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>