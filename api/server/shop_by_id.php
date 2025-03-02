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
            if(isset($_GET['shop_id']) && !empty($_GET['shop_id']))
            {
                $shop_id = validate_creds($_GET['shop_id']);
                
                $sql = "SELECT * FROM sklepy WHERE id_sklepu = :id_sklepu";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id_sklepu', $shop_id);
                $stmt -> execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($stmt->rowCount() === 1) 
                {
                    $data_to_send = 
                    [
                        "adres" => $row['adres'],
                        "lat" => $row['lat'],
                        "lon" => $row['lon'],
                        "suma_sprzedazy" => $row['suma_sprzedazy']
                    ];
                    echo json_encode($data_to_send, JSON_PRETTY_PRINT);
                }
                else
                {
                    $data_to_send = 
                    [
                        "adres" => null,
                        "lat" => null,
                        "lon" => null,
                        "suma_sprzedazy" => null
                    ];
                    echo json_encode($data_to_send, JSON_PRETTY_PRINT);
                }
            }

        }


    }

?>