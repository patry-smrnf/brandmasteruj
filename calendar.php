<?php
    include('scripts/php/database.php');
    include('scripts/php/security.php');
    include('scripts/php/config.php');

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
            <a href="map.html">
                <button class="header_button">
                    <img src="images/map-pin.svg" alt="Location">
                </button>
            </a>
        </div>
        <div class="container">
            <div class="main_body">
                <div class="column">
                <?php

                    $preview_adres = "???";
                    $preview_start_godz = 0;
                    $preview_end_godz = 0;
                    $preview_srednia_sprzedaz = 0;

                    $czy_jest_akcja_dzis = false;

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
                            $preview_srednia_sprzedaz = $row['suma_sprzedazy'];
                            $czy_jest_akcja_dzis = true;
                            break;
                        }
                    }
                    if($czy_jest_akcja_dzis)
                    {
                ?>
                    <div class="content">
                        <h1>Twoj punkt dzisiaj:</h1>
                        <div class="calendar_data_box data_box_active">
                            <h2>Punkt</h2>
                            <h1><?php echo $preview_adres; ?></h1>
                            <h2>Godziny</h2>
                            <h1><?php echo $preview_start_godz . ' - ' . $preview_end_godz; ?></h1>
                            <h2>Srednia sprzedaz</h2>
                            <h1><?php echo $preview_srednia_sprzedaz; ?></h1>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <div class="content">
                        <h1>Dodaj / Zmien ( Akcje ):</h1>
                        <a class="basic_button_a" href="calendar_editor.php"> 
                            Kliknij tutaj
                        </a>
                    </div>
                </div>
                <div class="column">
                    <div class="content">
                        <h1>Reszta miesiaca:</h1>
                        <?php 
                        foreach ($dates as $date) 
                        {
                            if (strtotime($date) > strtotime($today)) //tylko dni po dzisiaj
                            {
                                $adres_akcji = "BRAK";
                                $start_akcji = 0;
                                $koniec_akcji = 0;
                                $sprzedaz = 0;
                                
                                
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
                                        $sprzedaz = $row['suma_sprzedazy'];

                                        echo '                        
                                        <div class="calendar_data_box">
                                            <h2>Data</h2>
                                            <h1>'. $date .'</h1>
                                            <h2>Punkt</h2>
                                            <h1>'. $adres_akcji .'</h1>
                                            <h2>Godziny</h2>
                                            <h1>' . $start_akcji . ' - ' . $koniec_akcji .'</h1>
                                            <h2>Srednia sprzedaz</h2>
                                            <h1>'. $sprzedaz .'</h1>
                                        </div>';

                                        break; // Exit loop once found
                                    }
                                }
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>