<?php

    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');
    header('Content-Type: application/json');

    $auth_cookie_name = "auth_token";
    
    $cookie = validate_creds($_COOKIE[$auth_cookie_name]);
   
    $sql = "SELECT * FROM auth WHERE token = :token";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':token', $cookie);
    $stmt -> execute();
        
    if ($stmt->rowCount() === 1) 
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
       
        if ($row["token"] === $cookie)
        {
           echo json_encode(["Success" => "Poprawny Token"]);
        }
        else
        {
            echo json_encode(["Error" => "Niepoprawny Token"]);
        }
    }
    else
    {
        echo json_encode(["Error" => "Niepoprawny Token"]);
    }



?>