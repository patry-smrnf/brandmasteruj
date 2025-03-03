<?php
    include('scripts/php/security.php');
    include('scripts/php/web_functions.php');

    $status = is_logged();

    if(!$status['logged'])
    {
        header("Location: login.php");
        exit();
    }
    $id_miejsca = $_GET['id_miejsca'];
    $id_user = $status['id'];
    if(empty($id_miejsca) || $id_miejsca === "")
    {
        header("Location: map.php");
    }

    $year = date('Y');
    $month = date('m');
    $today = date('Y-m-d');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $data = [
            "lokalizacja_punkt" => $_POST['lokalizacja_punkt'],
            "punkt_godzina_start" => $_POST['punkt_godzina_start'],
            "punkt_godzina_koniec" => $_POST['punkt_godzina_koniec'],
            "akcja_data" => $_POST['punkt_data'],
            "akcja" => $_POST['submit']
        ];
        $punkt_data = $_POST['punkt_data'];
        $response = sendPost("http://localhost/brandmasteruj_v2/api/user/save_event.php", $data, $_COOKIE['auth_token']);
        header("Location: calendar_editor.php?active_day=$punkt_data");
        exit();
    }


    $shop_data = fetchGet("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_miejsca");
    $my_month = fetchGet("http://localhost/brandmasteruj_v2/api/user/my_month.php?month=$month&year=$year");

    $adres_sklepu = $shop_data['adres'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/basic.css" rel="stylesheet">
        <link href="css/form_styles.css" rel="stylesheet">
        <link href="css/calendar.css" rel="stylesheet">
        <link href="css/map_editor.css" rel="stylesheet">

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
                        <h1>Wybrany przez ciebie punkt:</h1>
                        <div class="calendar_data_box">
                            <h2>Nazwa</h2>
                            <h1><?php echo $adres_sklepu; ?></h1>
                        </div>
                        <div class="calendar">
                            <div class="days">
                              <span>Mon</span>
                              <span>Tue</span>
                              <span>Wed</span>
                              <span>Thu</span>
                              <span>Fri</span>
                              <span>Sat</span>
                              <span>Sun</span>
                            </div>
                            <div class="dates">
                                <?php
                                    foreach($my_month['akcje'] as $akcja)
                                    {
                                        if($akcja['date'] === $today)
                                        {
                                            echo '                                
                                            <button onclick="set_map_editor(\''. $akcja['date']. '\')" class="today">
                                                <time>'.date('d', strtotime($akcja['date'])).'</time>
                                            </button>';
                                        }
                                        if($akcja['miejsce'] !== null)
                                        {
                                            echo '                                
                                            <button onclick="set_map_editor(\''. $akcja['date']. '\')" class="selected">
                                                <time>'.date('d', strtotime($akcja['date'])).'</time>
                                            </button>';
                                        }
                                        else
                                        {
                                            echo '                                
                                            <button onclick="set_map_editor(\''. $akcja['date']. '\')">
                                                <time>'.date('d', strtotime($akcja['date'])).'</time>
                                            </button>';
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <form method="POST" action="map_editor.php">
                            <h2>W Godzinach: </h2>
                            <div class="field padding-bottom--24">
                                <input type="text" value="0" name="punkt_godzina_start" id="punkt_godzina_start">
                                <input type="text" value="0" name="punkt_godzina_koniec" id="punkt_godzina_koniec">
                            </div>
                            <input type="hidden" value="<?php echo $adres_sklepu; ?>" name="lokalizacja_punkt" id="lokalizacja_punkt">
                            <input type="hidden" value="0" name="punkt_data" id="data_input">
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Zmien">
                            </div>
                        </form>
                        <script>
                            function set_map_editor(date)
                            {
                                var input_data = document.getElementById('data_input');
                                input_data.value = `${date}`;
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>