<?php

function czy_istnieje_akcja($id_akcji, $id_user)
{
    include(__DIR__ . '/../scripts/database.php');

    $sql = "SELECT * FROM `akcje` WHERE `id_akcji` = :id_akcji AND `id_user` = :id_user";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':id_akcji', $id_akcji);
    $stmt -> bindParam(':id_user', $id_user);

    $stmt -> execute();

    if ($stmt->rowCount() === 1)
    {
        return true;
    }
    else
    {
        return false;
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    header('Content-Type: application/json');

    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');

    $url = "http://localhost/brandmasteruj_v2/api/server/get_id.php"; //api to zdobywania id usera i danych poprzez token

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

            $id_akcji = validate_creds($data_from_user['id_akcji']);
            $ilosc_sprzedazy = intval(validate_creds($data_from_user['ilosc_sprzedazy']));
            $typ_sprzedazy = validate_creds($data_from_user['typ_sprzedazy']);
            $co_robimy = validate_creds($data_from_user['action']);

            switch($co_robimy)
            {
                case "Dodaj":
                    if(czy_istnieje_akcja($id_akcji, $user_id))
                    {
                        $sql = "SELECT * FROM `sprzedaze` WHERE `id_akcji` = :id_akcji";
                        $stmt = $pdo->prepare($sql);
                        $stmt -> bindParam(':id_akcji', $id_akcji);
                        $stmt -> execute();
            
                        // Sprawdzanie czy nie istnieje juz przypisane sprzedaze dla tej akcji
                        if ($stmt->rowCount() === 0)
                        {
                            
                            for($counter = 0; $counter<$ilosc_sprzedazy; $counter++)
                            {
                                $sql = "INSERT INTO `sprzedaze`(`id_user`, `id_akcji`, `typ_sprzedazy`) VALUES (:id_user,:id_akcji,:typ_sprzedazy)";
                                $stmt = $pdo->prepare($sql);
                                $stmt -> bindParam(':id_user', $user_id);
                                $stmt -> bindParam(':id_akcji', $id_akcji);
                                $stmt -> bindParam(':typ_sprzedazy', $typ_sprzedazy);
                                $stmt -> execute();
                            }
                            echo json_encode(["Success" => "Dodano $ilosc_sprzedazy sprzedazy $typ_sprzedazy"]);
                        }
                        else
                        {
                            echo json_encode(["Error" => "Nie mozesz dodac sprzedazy do akcji gdzie juz dodales"]);
                        }
                    }
                    else
                    {
                        echo json_encode(["Error" => "Nie istnieje taka akcja dla tego user"]);
                    }
                    break;
            }


        }

    }
}


?>