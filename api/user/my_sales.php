<?php
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
            $user_id = $data['id'];
            $data_to_send = 
            [
                "sprzedaze" => []
            ];

            if(isset($_GET['id_sklep']))
            {
                //znalezienie wszystkich akcji usera naszego w danym skelpie
                $id_sklepu = validate_creds($_GET['id_sklep']);

                $sql = "SELECT * FROM akcje WHERE id_user = :id_user AND id_sklepu = :id_sklepu";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id_user', $user_id);
                $stmt -> bindParam(':id_sklepu', $id_sklepu);
                $stmt -> execute();
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                //zbieranie wszystkich sprzedazy bedacych w akcjach ktore byly w konkretnym sklepie
                foreach($records as $akcja)
                {
                    $id_akcji = $akcja['id_akcji'];

                    $sql = "SELECT * FROM sprzedaze WHERE id_user = :id_user AND id_akcji = :id_akcji";
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':id_user', $user_id);
                    $stmt -> bindParam(':id_akcji', $id_akcji);
                    $stmt -> execute();
                    $sprzedaze = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($sprzedaze as $sell)
                    {
                        $data_to_send["sprzedaze"][] = 
                        [
                            "id_sprzedazy" => $sell['id_sprzedazy'],
                            "id_akcji" => $sell['id_akcji'],
                            "typ_sprzedazy" => $sell['typ_sprzedazy'],
                            "data_dodania" => $sell['godzina_dodania'],
                        ];
                    }
                }
            }
            else
            {
                $sql = "SELECT * FROM sprzedaze WHERE id_user = :id_user";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id_user', $user_id);
                $stmt -> execute();
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                foreach($records as $sell)
                {
                    $data_to_send["sprzedaze"][] = 
                    [
                        "id_sprzedazy" => $sell['id_sprzedazy'],
                        "id_akcji" => $sell['id_akcji'],
                        "typ_sprzedazy" => $sell['typ_sprzedazy'],
                        "data_dodania" => $sell['godzina_dodania'],
                    ];
                }
            }
            echo json_encode($data_to_send, JSON_PRETTY_PRINT);

        }
    }


?>