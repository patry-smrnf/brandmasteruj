<?php
include("scripts/php/web_functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST["login"]) && !empty($_POST["login"])) 
    {
        $data = [
            "login" => $_POST['login']
        ];
        $response = sendPost("http://localhost/brandmasteruj_v2/api/user/auth_me.php", $data);
        $data = json_decode($response, true); 
        
        if (!empty($data['Success']) && $data['Success'] === "Zalogowano") 
        {
            // Set the cookie before any output is sent
            $nazwa_cookie = "auth_token";
            $auth_cookie_time = time() + 86400; // 1 day expiration time

            setcookie($nazwa_cookie, $data['Token'], $auth_cookie_time, "/", "", false, true); // Using Secure, HttpOnly flags (false for secure if no HTTPS)
            
            header("Location: index.php");
            exit(); 
        }
    }
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/basic.css" rel="stylesheet">
        <link href="css/form_styles.css" rel="stylesheet">
    </head>
    <body>
        <div class="header_top">
        </div>
        <div class="container">
            <div class="main_body">
                <div class="column">
                    <div class="content">
                        <h1>Login:</h1>
                        <form action="login.php" method="POST">
                            <div class="field padding-bottom--24">
                                <input type="text" name="login">
                            </div>
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Zaloguj">
                            </div>
                        </form>
                        <?php 
                        if(!empty($data['Error']))
                        {
                            echo '<a class="error_text">' . $data['Error'] . '</a>';
                        }
                        ?>
                    </div>
                    <div class="content">
                        <h1>Stworz nowy login:</h1>
                        <a class="basic_button_a" href="register.php"> 
                            Kliknij Tutaj
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>