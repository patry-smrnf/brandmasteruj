<?php
include("./scripts/php/database.php");
include("./scripts/php/security.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST["login"]) && !empty($_POST["login"])) 
    {
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
                        <?php if(isset($_GET['error'])) 
                        {
                             ?>
                        <a class="error_text"><?php echo $_GET['error']?></a>
                        <?php }?>
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