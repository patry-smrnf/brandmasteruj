<?php
    include('scripts/php/database.php');
    include('scripts/php/security.php');
    include('scripts/php/config.php');
    require_once('scripts/php/user_session.php');


    is_logged();

    $year = date('Y');
    $month = date('m');

    $days_in_month = date('t', strtotime("$year-$month-01"));

    //echo $days_in_month;

    //wyliczenie wszystkich dni w tym miesiacu
    $dates = [];
    for ($day = 1; $day <= $days_in_month; $day++) 
    {
        $dates[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    //dzisiaj
    $today = date('Y-m-d');

    $id_user = UserSession::getUserId();

    $sql = "SELECT * FROM akcje WHERE id_user = :id_user";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':id_user', $id_user);
    $stmt -> execute();

    //zgranie calej bazy akcji dotyczacej usera
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($records);

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
            <a href="map.html">
                <button class="header_button">
                    <img src="images/map-pin.svg" alt="Location">
                </button>
            </a>
        </div>
        <div class="container">
            <div class="main_body">
                <div class="calendar_picked_day">
                    <a id="top"></a>
                    <div class="calendar_picked_day_title" id="picked_day_title">
                        <a id="punkt_data">Dzisiaj</a>
                    </div>
                    <div class="calendar_picked_day_events">
                    
                    <?php
                    $preview_adres = "???";
                    $preview_start_godz = 0;
                    $preview_end_godz = 0;

                    foreach ($records as $record) 
                    {
                        if ($record['data'] === $today) 
                        {
                            $id_sklepu = $record['id_sklepu'];
                            $preview_start_godz = $record['start_godzina'];
                            $preview_end_godz = $record['koniec_godzina'];

                            $sql = "SELECT * FROM baza_sklepow WHERE id_sklepu = :id_sklepu";
                            $stmt = $pdo->prepare($sql);
                            $stmt -> bindParam(':id_sklepu', $id_sklepu);
                            $stmt -> execute();

                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            $preview_adres = $row['adres_sklepu'];
                            break;
                        }
                    }
                    echo '<form action="scripts/php/save_event.php" method="POST">
                            <h2>Punkt: </h2>
                            <div class="autocomplete" style="width:300px;">
                                <input id="myInput" type="text" name="lokalizacja_punkt" value="'. $preview_adres .'" placeholder="Adres">
                            </div>
                            <h2>W Godzinach: </h2>
                            <div class="field padding-bottom--24">
                                <input type="text" value="'. $preview_start_godz .'" name="punkt_godzina_start" id="punkt_godzina_start">
                                <input type="text" value="'. $preview_end_godz .'" name="punkt_godzina_koniec" id="punkt_godzina_koniec">
                            </div>
                            <input type="hidden" id="data_input" name="date" value="'. $today .'">
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Zmien">
                            </div>
                            <div class="field padding-bottom--24 the_red_scary_one">
                                <input type="submit" name="submit" value="Usun">
                            </div>
                        </form>';
                        ?>
                        <?php if(isset($_GET['error'])) 
                        {
                             ?>
                        <a class="error_text"><?php echo $_GET['error']?></a>
                        <?php }?>
                        <script src="scripts/js/autocomplete_addresses.js"></script>
                    </div>
                </div>
                <div class="calendar_picker_editor">
                    <ul>
                        <?php
                            foreach($dates as $date)
                            {
                                $adres_akcji = "BRAK";
                                $start_akcji = 0;
                                $koniec_akcji = 0;

                                foreach ($records as $record) 
                                {
                                    if ($record['data'] === $date) {
                                        $id_sklepu = $record['id_sklepu'];
                                        $start_akcji = $record['start_godzina'];
                                        $koniec_akcji = $record['koniec_godzina'];

                                        $sql = "SELECT * FROM baza_sklepow WHERE id_sklepu = :id_sklepu";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt -> bindParam(':id_sklepu', $id_sklepu);
                                        $stmt -> execute();

                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                        $adres_akcji = $row['adres_sklepu'];
                                        break; // Exit loop once found
                                    }
                                }

                                if($date === $today)
                                {
                                    echo '<li href="#top" class="today" id="date_calendar_window" onclick="set_editor(\''. $adres_akcji. '\', \''. $start_akcji .'\', \''. $koniec_akcji.'\', \''. $date .'\')"><time datetime="'.$date.'">'.date('d', strtotime($date)).'</time><a>'.$adres_akcji.'</a> 
                                    <a>'.$start_akcji . '-'.$koniec_akcji .'</a></li>';

                                }
                                else
                                {
                                    echo '<li href="#top" id="date_calendar_window" onclick="set_editor(\''. $adres_akcji. '\', \''. $start_akcji .'\', \''. $koniec_akcji.'\', \''. $date .'\')"><time datetime="'.$date.'">'.date('d', strtotime($date)).'</time><a>'.$adres_akcji.'</a> 
                                    <a>'.$start_akcji . '-'.$koniec_akcji .'</a></li>';
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>