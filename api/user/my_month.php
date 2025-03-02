<?php
    function getDaysInMonth($month, $year)  // Rozpisanie wszystkich dni w miesiacu danego roku
    {
        $days = [];

        $startDate = new DateTime("$year-$month-01");

        $endDate = new DateTime("$year-$month-" . $startDate->format('t'));

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

        foreach ($dateRange as $date) {
            $days[] = $date->format('Y-m-d'); // Format: YYYY-MM-DD
        }

        return $days;
    }

    header('Content-Type: application/json');

    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');

    //sprawdzanie czy request zostal wyslany przez osobe ktora ma auth
    $url = "http://localhost/brandmasteruj_newBackend/api/server/get_id.php";

    $cookies = [];
    foreach ($_COOKIE as $key => $value) {
        $cookies[] = "$key=$value";
    }
    $cookieString = implode("; ", $cookies);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification (use only if necessary)
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cookie: $cookieString"]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) 
    {
        echo json_encode(["Error" => curl_error($ch)]);
    } 
    else 
    {
        $data = json_decode($response, true);
        if(isset($data['Error']) && !empty($data['Error']))
        {
            echo json_encode(["Error" => "Error with auth"]);
        }
        else
        {
            $sql = "SELECT * FROM akcje WHERE id_user = :id_user";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id_user', $data['id']);
            $stmt -> execute();
        
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if(isset($_GET['month']) && !empty($_GET['month']) && isset($_GET['year']) && !empty($_GET['year']))
            {
                $data_to_send = 
                [
                    "akcje" => []
                ];

                $month_user = validate_creds($_GET['month']);
                $year_user = validate_creds($_GET['year']);
                
                $dni = getDaysInMonth($month_user, $year_user);
                foreach($dni as $dzien)
                {
                    $akcja_jest = false;
                    foreach($records as $akcja)
                    {
                        if($akcja['data'] === $dzien)
                        {
                            $data_to_send["akcje"][] = 
                            [
                                "miejsce" => $akcja['id_sklepu'],
                                "start" => $akcja['start_godzina'],
                                "koniec" => $akcja['koniec_godzina'],
                                "sprzedaze" => $akcja['sprzedaze'],
                                "date" => $dzien,
                            ];
                            $akcja_jest = true;
                            break;
                        }
                    }
                    if(!$akcja_jest)
                    {
                            $data_to_send["akcje"][] = 
                            [
                                "miejsce" => null,
                                "start" => null,
                                "koniec" => null,
                                "sprzedaze" => null,
                                "date" => $dzien,
                            ];
                    }

                }
    
                echo json_encode($data_to_send, JSON_PRETTY_PRINT);
            }
            else
            {
                echo json_encode(["Error" => "Brak wpisanego ktory miesiac i rok"]);
            }
        }
    }
    curl_close($ch);

?>