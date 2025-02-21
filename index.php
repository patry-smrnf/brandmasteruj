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
                    
                    echo '
                    <div class="content">
                        <h1>Twoj punkt dzisiaj:</h1>
                        <div class="calendar_data_box">
                            <h2>Nazwa</h2>
                            <h1>'.$preview_adres.'</h1>
                        </div>
                        <h1>Jak tobie poszlo?</h1>
                        <form action="POST">
                            <div class="field padding-bottom--24">
                                <input type="text" name="sprzedaz">
                            </div>
                            <input type="hidden" id="data_input" name="date" value="'. $today .'">
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Dodaj">
                            </div>
                        </form>
                    </div>';
                    }?>
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
                <?php
                    $suma_godzin_przed_dzisiaj = 0;
                    $suma_godzin_po = 0;

                    foreach($records as $record)
                    {
                        if (strtotime($record['data']) < strtotime($today)) //tylko dni po dzisiaj
                        {
                            //$suma_godzin_przed_dzisiaj += $record['koniec_godzina'] - $record['start_godzina'];
                            $start = new DateTime($record['start_godzina']);
                            $end = new DateTime($record['koniec_godzina']);
                            $diff = $start->diff($end);
                            
                            // Convert the difference to total hours
                            $totalHours = $diff->h + ($diff->i / 60) + ($diff->s / 3600);
                            
                            $suma_godzin_przed_dzisiaj += $totalHours;
                            //$suma_godzin_przed_dzisiaj += $time2->diff($time1);

                        }
                        if (strtotime($record['data']) >= strtotime($today)) //tylko dni po dzisiaj
                        {
                            $start = new DateTime($record['start_godzina']);
                            $end = new DateTime($record['koniec_godzina']);
                            $diff = $start->diff($end);
                            
                            // Convert the difference to total hours
                            $totalHours = $diff->h + ($diff->i / 60) + ($diff->s / 3600);
                            
                            $suma_godzin_po += $totalHours;
                            
                            //$suma_godzin_po += $time2->diff($time1);
                        }

                    }
                ?>
                <div class="column">
                    <div class="content">
                        <h1>Ilosc godzin w tym miesiacu</h1>
                        <a class="text_important"><?php echo $suma_godzin_przed_dzisiaj; ?></a>
                        <h1>Twoje sprzedaze w tym miesiacu</h1>
                        <a class="text_important">11</a>
                        <hr class="solid">
                        <h1>Ilosc zadeklerowanych godzin</h1>
                        <a class="text_important"><?php echo intval($suma_godzin_przed_dzisiaj + $suma_godzin_po);?></a>
                        <h1>Twoja efektywnosc</h1>
                        <a class="text_important">2.1</a>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>