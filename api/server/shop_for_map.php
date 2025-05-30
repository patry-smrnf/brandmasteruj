<?php
    header('Content-Type: application/json');

    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');

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
            $data_to_send = [];

            $user_id = $data['id'];

            $sql = "SELECT * FROM `sklepy`";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($rows as $row)
            {
                $id_sklepu = $row['id_sklepu'];
                $ilosc_sprzedazy_twoja = 0;
                $ilosc_sprzedazy_ogolna = 0;

                //znajdywanie info dot ilosci sprzedazy POD KONKRETNEGO USER
                $sql = "SELECT * FROM akcje WHERE id_user = :id_user AND id_sklepu = :id_sklepu";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id_user', $user_id);
                $stmt -> bindParam(':id_sklepu', $id_sklepu);
                $stmt -> execute();
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($records as $akcja)
                {
                    $id_akcji = $akcja['id_akcji'];

                    $sql = "SELECT * FROM sprzedaze WHERE id_user = :id_user AND id_akcji = :id_akcji";
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':id_user', $user_id);
                    $stmt -> bindParam(':id_akcji', $id_akcji);
                    $stmt -> execute();
                    $sprzedaze = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $ilosc_sprzedazy_twoja = count($sprzedaze);
                }

                //znajdywanie info dot ilosci sprzedazy CALEGO ZESPOL
                $sql = "SELECT * FROM akcje WHERE id_sklepu = :id_sklepu";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id_sklepu', $id_sklepu);
                $stmt -> execute();
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($records as $akcja)
                {
                    $id_akcji = $akcja['id_akcji'];

                    $sql = "SELECT * FROM sprzedaze WHERE id_akcji = :id_akcji";
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':id_akcji', $id_akcji);
                    $stmt -> execute();
                    $sprzedaze = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $ilosc_sprzedazy_ogolna = count($sprzedaze);
                }


                $data_to_send[] = [
                    "id_sklepu" => $row['id_sklepu'],
                    "adres" => $row['adres'],
                    "lat" => $row['lat'],
                    "lon" => $row['lon'],
                    "ilosc_sprzedazy_twoja" => $ilosc_sprzedazy_twoja,
                    "ilosc_sprzedazy_ogolna" => $ilosc_sprzedazy_ogolna
                ];
            }
            echo json_encode($data_to_send, JSON_PRETTY_PRINT);
        }

    }


?>