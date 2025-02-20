<?php

function auth_user($login)
{
    include('database.php');
    include('config.php');

    $token = bin2hex(random_bytes(50/2));
    $nazwa_cookie = $auth_cookie_name;

    try
    {
        $sql = "INSERT INTO auth (token, login) VALUES (:token, :login)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        setcookie( $nazwa_cookie, $token, $auth_cookie_time, "/"); // 86400 = 1 day
    }
    catch(Exception $e)
    {
        return false;
    }
}

function is_logged()
{
    include('config.php');
    include('database.php');
    require_once('user_session.php');

    if(isset($_COOKIE[$auth_cookie_name]) && !empty($_COOKIE[$auth_cookie_name] && validate_token()))
    {
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
                $user_login = $row['login'];

                $sql = "SELECT * FROM users WHERE login_user = :login_user";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':login_user', $user_login);
                $stmt -> execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $user_id = $row['id_user'];


                UserSession::init($user_id, $user_login, $cookie);
            }
        }
        return true;
    }
    else
    {
        header("Location: login.php");
        exit();
    }
}

function validate_token()
{
    include('config.php');
    include('database.php');
    $cookie = validate_creds($_COOKIE[$auth_cookie_name]);
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
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    catch(Exception $e)
    {
        echo $e;
        return false;
    }

}

function validate_creds($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>