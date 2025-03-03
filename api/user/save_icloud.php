<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    header('Content-Type: application/json');

    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');

    $url = "http://localhost/brandmasteruj_v2/api/server/get_id.php";

    $cookies = [];
    foreach ($_COOKIE as $key => $value) {
        $cookies[] = "$key=$value";
    }
    $cookieString = implode("; ", $cookies);

    $headers = getallheaders(); // Get all headers
    $auth_token = $headers['auth'];
    
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification (use only if necessary)
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Cookie: auth_token=$auth_token"
    ]);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) 
    {
        echo json_encode(["Error" => curl_error($ch)]);
    } 
    else 
    {
        $data = json_decode($response, true);
        if(isset($data['Error']) && !empty($data['Error'])) //jesli jest kurwa blad z auth
        {
            echo json_encode(["Error" => "Problem z auth"]);
        }
        else
        {
            $user_id = $data['id'];

            $json = file_get_contents("php://input");
            $data_from_user = json_decode($json, true);
            $year = validate_creds($data_from_user['year_actual']);
            $month = validate_creds($data_from_user['month_actual']);
            $co_robimy = validate_creds($data_from_user['akcja']);

            if((!empty($co_robimy)))
            {

                $ch = curl_init("http://localhost/brandmasteruj_v2/api/user/my_month.php?month=$month&year=$year");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification (use only if necessary)
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Cookie: auth_token=$auth_token"
                ]);
                
                $my_month = curl_exec($ch);
                $my_month_decoded = json_decode($my_month, true);


                $adresy_akcji = [];

                //icloud stuff tutaj
                $ical_content = "BEGIN:VCALENDAR\r\n";
                $ical_content .= "VERSION:2.0\r\n";
                $ical_content .= "PRODID:-//Brandmasterujsb//NONSGML v1.0//EN\r\n";
                
                foreach($my_month_decoded['akcje'] as $akcja)
                {
                    if($akcja['miejsce'] !== null)
                    {
                        $id_miejsca = $akcja['miejsce'];
                        $ch = curl_init("http://localhost/brandmasteruj_v2/api/server/shop_by_id.php?shop_id=$id_miejsca");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification (use only if necessary)
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            "Cookie: auth_token=$auth_token"
                        ]);
                        $shop_data = curl_exec($ch);
                        $shop_data_decoded = json_decode($shop_data, true);                        
                        $adres_akcji = $shop_data_decoded['adres'];
                        
                        //przypisywanie podstawowych danych dla wydarzenia
                        $event_title = htmlspecialchars("Brandmasterowanie");
                        $event_description = htmlspecialchars($adres_akcji);
                        $event_location = htmlspecialchars($adres_akcji);

                        $start_datetime = $akcja['date'] . ' ' . $akcja['start'];
                        $end_datetime = $akcja['date'] . ' ' . $akcja['koniec'];

                        //konwertowanie do formatu icloud daty i godziny
                        $start_date = date("Ymd\THis", strtotime($start_datetime));
                        $end_date = date("Ymd\THis", strtotime($end_datetime));

                        $ical_content .= "BEGIN:VEVENT\r\n";
                        $ical_content .= "UID:" . uniqid() . "@brandmasteruj.pzdr\r\n";
                        $ical_content .= "DTSTAMP:" . date("Ymd\THis") . "Z\r\n";
                        $ical_content .= "DTSTART:$start_date\r\n";
                        $ical_content .= "DTEND:$end_date\r\n";
                        $ical_content .= "SUMMARY:$event_title\r\n";
                        $ical_content .= "DESCRIPTION:$event_description\r\n";
                        $ical_content .= "LOCATION:$event_location\r\n";
                        $ical_content .= "END:VEVENT\r\n";
                    }
                }

                $ical_content .= "END:VCALENDAR\r\n";

                $file_path = "../../events_$user_id.ics";
                $file_path_for_user = "events_$user_id.ics";
                file_put_contents($file_path, $ical_content);
                
                // Redirect to the ICS file to trigger automatic opening in iCloud Calendar
                echo json_encode(["Succcess" => $file_path_for_user]);
            }
        }
    }
}


?>