<?php
include("./scripts/php/database.php");
include("./scripts/php/security.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST["submit"]) && !empty($_POST["submit"])) 
    {
        $login = bin2hex(random_bytes(10/2));
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        try
        {
            $sql = "INSERT INTO users (login_user, user_agent) VALUES (:login_user, :user_agent)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':login_user', $login);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->execute();

            header("Location: register.php?message=$login");
            exit();

        }
        catch(Exception $e)
        {
            header("Location: register.php?error=Blad w rejestrowaniu");
            exit();
        }
    }
    else
    {
        header("Location: register.php?error=Blad w zapytaniu");
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
                    <?php
                     if(!isset($_GET['message']))
                     {

                        ?>
                    <div class="content">
                        <h1>Stworz nowy login:</h1>
                        <form action="register.php" method="POST">
                            <div class="field padding-bottom--24">
                                <input type="submit" name="submit" value="Generuj">
                            </div>
                        </form>
                        <?php if(isset($_GET['error'])) 
                        {
                             ?>
                        <a class="error_text"><?php echo $_GET['error']?></a>
                        <?php }?>
                    </div>
                    <?php
                    }
                    else
                    {?>
                    <div class="content">
                        <h1>Twoj nowy login to:</h1>
                        <h2><?php echo $_GET['message']?></h2>
                        <a class="basic_button_a" href="index.html"> 
                            Strona glowna
                        </a>
                    </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>