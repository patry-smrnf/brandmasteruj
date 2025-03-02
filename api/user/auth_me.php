<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    include(__DIR__ . '/../scripts/database.php');
    include(__DIR__ . '/../scripts/security.php');

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    $login = validate_creds($data['login']);

    $sql = "SELECT * FROM users WHERE login_user = :login_user";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':login_user', $login);
    $stmt -> execute();
    if ($stmt->rowCount() === 1) 
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row["login_user"] === $login)
        {
            //tworzenie tokenu
            $token = bin2hex(string: random_bytes(50/2));
            $sql = "INSERT INTO auth (token, login) VALUES (:token, :login)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':login', $login);
            $stmt->execute();

            echo json_encode(["Success" => "Zalogowano", "Token" => $token]);
        }
    }
    else
    {
        echo json_encode(["Error" => "Bledny Login"]);
    }
}
else
{
    echo json_encode(["Error" => "Zly usage api"]);
}


?>