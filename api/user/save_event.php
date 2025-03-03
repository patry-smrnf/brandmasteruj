<?php

function format_hours($hour)
{
    if (strpos($hour, ":") === false) 
    {
        $formatted_time = $hour . ":00:00"; 
    }
    else {
        $formatted_time = $hour . ":00"; 
    }

    return $formatted_time;
}

function czy_istnieje_adres($adres)
{
    include(__DIR__ . '/../scripts/database.php');
    //sprawdzenie czy istnieje taki adres w bazie danych
    $sql = "SELECT * FROM sklepy WHERE adres = :adres_sklepu";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':adres_sklepu', $adres);
    $stmt -> execute();

    if (($stmt->rowCount() === 1))  //jesli istnieje taki adres
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_puntku = $row['id_sklepu'];
        $stara_ilosc_godzin = $row['suma_sprzedazy'];
        return [
            "Istnieje" => true,
            "id" => $id_puntku,
            "sprzedaze" => $stara_ilosc_godzin
        ];
    }
    else
    {
        return [
            "Istnieje" => false
        ];
    }
}

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
        if(isset($data['Error']) && !empty($data['Error']))
        {
            echo json_encode(["Error" => "Problem z auth"]);
        }
        else
        {
            $user_id = $data['id'];

            $json = file_get_contents("php://input");
            $data_from_user = json_decode($json, true);

            $lokalizacja_adres = validate_creds($data_from_user['lokalizacja_punkt']);
            $start_akcji = format_hours(validate_creds($data_from_user['punkt_godzina_start']));
            $koniec_akcji = format_hours(validate_creds($data_from_user['punkt_godzina_koniec']));
            $data_akcji = validate_creds($data_from_user['akcja_data']);
            $co_robimy = validate_creds($data_from_user['akcja']);


            if((!empty($lokalizacja_adres) && !empty($start_akcji) && !empty($koniec_akcji)))
            {
                if($co_robimy === "Zmien")
                {
                    $stan_adresu = czy_istnieje_adres($lokalizacja_adres);
                    if($stan_adresu['Istnieje'])
                    {
                        //sprawdzenie czy istnieje juz akcja dla tego usera tego dnia
                        $sql = "SELECT * FROM akcje WHERE data = :data AND id_user = :id_user";
                        $stmt = $pdo->prepare($sql);
                        $stmt -> bindParam(':data', $data_akcji);
                        $stmt -> bindParam(':id_user', $user_id);
                        $stmt -> execute();

                        //jesli istnieja to sie jedynie zaaktualizuje adres wpisany oraz godziny
                        if ($stmt->rowCount() === 1) 
                        {
                            try
                            {
                                $sql = "UPDATE akcje SET id_sklepu = :id_sklepu, start_godzina = :start_godzina, koniec_godzina = :koniec_godzina WHERE id_user = :id_user AND data = :data";
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT); // Added PARAM_INT if $id_user is an integer
                                $stmt->bindParam(':id_sklepu', $stan_adresu['id'], PDO::PARAM_INT); // Added PARAM_INT if $id_sklepu is an integer
                                $stmt->bindParam(':data', $data_akcji); // If $data_akcji is a string or date
                                $stmt->bindParam(':start_godzina', $start_akcji); // Make sure this is a valid time format
                                $stmt->bindParam(':koniec_godzina', $koniec_akcji); // Same here for valid time format
                                $stmt->execute();
                                echo json_encode(["Success" => "Zaktualizowano"]);

                            }
                            catch(Exception $e)
                            {
                                echo json_encode(["Error" => $e]);
                            }
                        }
                        else
                        {
                            try
                            {
                                // Insert new action into the `akcje` table
                                $sql = "INSERT INTO `akcje` (`id_user`, `id_sklepu`, `data`, `start_godzina`, `koniec_godzina`) 
                                        VALUES (:id_user, :id_sklepu, :data, :start_godzina, :koniec_godzina)";
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT); // Added PARAM_INT if $id_user is an integer
                                $stmt->bindParam(':id_sklepu', $stan_adresu['id'], PDO::PARAM_INT); // Added PARAM_INT if $id_sklepu is an integer
                                $stmt->bindParam(':data', $data_akcji); // If $data_akcji is a string or date
                                $stmt->bindParam(':start_godzina', $start_akcji); // Make sure this is a valid time format
                                $stmt->bindParam(':koniec_godzina', $koniec_akcji); // Same here for valid time format
                                $stmt->execute();
                                echo json_encode(["Success" => "Dodano akcje"]);

                            }
                            catch(Exception $e)
                            {
                                echo json_encode(["Error" => $e]);
                            }
                        }
                    }
                    else
                    {
                        echo json_encode(["Error" => "Nie istnieje taki adres"]);
                    }
                }
                if($co_robimy === "Usun")
                {               

                    //sprawdzenie czy istnieje juz akcja dla tego usera tego dnia
                    $sql = "SELECT * FROM akcje WHERE data = :data AND id_user = :id_user";
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':data', $data_akcji);
                    $stmt -> bindParam(':id_user', $user_id);
                    $stmt -> execute();
            
                    if($stmt->rowCount() === 1)
                    {
                        $sql = "DELETE FROM akcje WHERE data = :data AND id_user = :id_user";
                        $stmt = $pdo->prepare($sql);
                        $stmt -> bindParam(':data', $data_akcji);
                        $stmt -> bindParam(':id_user', $user_id);
                        $stmt -> execute();
                        echo json_encode(["Success" => "Usunieto akcje"]);

                    }
                    else
                    {
                        echo json_encode(["Error" => "Usuwasz cos co nie istnieje"]);

                    }
                }
                
            }
            else
            {
                echo json_encode(["Error" => "Brak Danych"]);
            }

        }
    }
}

?>