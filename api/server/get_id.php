<?php

    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');

    header('Content-Type: application/json');
    
    if(!empty($_COOKIE['auth_token']))
    {  
        $cookie = validate_creds($_COOKIE['auth_token']);
    
        try
        {
            $sql = "SELECT * FROM auth WHERE token = :token";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':token', $cookie);
            $stmt -> execute();
            
            if ($stmt->rowCount() === 1) 
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row["token"] === $cookie)
                {
                    $login = $row['login'];
                    $sql = "SELECT * FROM users WHERE login_user = :login_user";
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':login_user', $login);
                    $stmt -> execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $data = 
                    [
                        "login" => $login,
                        "id" => $row['id_user']
                    ];
                    echo json_encode($data, JSON_PRETTY_PRINT);
                }
                else
                {
                    $data = [
                        "Error" => "101b"
                    ];
                    echo json_encode($data, JSON_PRETTY_PRINT);
                }
            }
            else
            {
                $data = [
                    "Error" => "101a"
                ];
                echo json_encode($data, JSON_PRETTY_PRINT);
            }
        }
        catch(Exception $e)
        {
            echo $e;
        }
    }
    else
    {
        $data = [
            "Error" => "101c"
        ];
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
?>