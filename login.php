<?php
<<<<<<< HEAD
include("scripts/php/web_functions.php");
=======
include("./scripts/php/database.php");
include("./scripts/php/security.php");

>>>>>>> 16d77664d16c2f5a0b7114fdfec7453fbe48e5c6

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST["login"]) && !empty($_POST["login"])) 
    {
<<<<<<< HEAD
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
=======
        $login = validate_creds($_POST["login"]);
        try
        {
            $sql = "SELECT * FROM users WHERE login_user = :login_user";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':login_user', $login);
            $stmt -> execute();

            if ($stmt->rowCount() === 1) 
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row["login_user"] === $login)
                {
                    if(auth_user($login))
                    {
                        header("Location: login.php?error=Blad w tworzeniu tokena");
                        exit();
                    }
                    header("Location: index.php");
                    exit();
                }
            }
            else
            {
                header("Location: login.php?error=Bledny Login");
                exit();
            }
        }
        catch(Exception $e)
        {
            header("Location: login.php?error=$e");
            exit();
        }
    }
    else
    {
        header("Location: login.php?error=Pusty login");
        exit();
    }

}

>>>>>>> 16d77664d16c2f5a0b7114fdfec7453fbe48e5c6
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
<<<<<<< HEAD
                        <?php 
                        if(!empty($data['Error']))
                        {
                            echo '<a class="error_text">' . $data['Error'] . '</a>';
                        }
                        ?>
=======
                        <?php if(isset($_GET['error'])) 
                        {
                             ?>
                        <a class="error_text"><?php echo $_GET['error']?></a>
                        <?php }?>
>>>>>>> 16d77664d16c2f5a0b7114fdfec7453fbe48e5c6
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