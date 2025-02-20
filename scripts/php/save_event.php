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

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    require_once('user_session.php');
    include('database.php');
    include('security.php');

    is_logged();

    if($_POST['submit'] === "Zmien")
    {
        if($_POST['lokalizacja_punkt'] === "BRAK" || intval($_POST['punkt_godzina_start']) === 0 || intval($_POST['punkt_godzina_koniec']) === 0 )
        {
            header("Location: ../../calendar_editor.php?error=Brakujace dane");
            exit();
        }
        else
        {
            $adres_puntku = validate_creds($_POST['lokalizacja_punkt']);
            $start_akcji_godzina = format_hours(validate_creds($_POST['punkt_godzina_start']));
            $koniec_akcji_godzina = format_hours(validate_creds($_POST['punkt_godzina_koniec']));
            $data_akcji = validate_creds($_POST['date']);
            $ilosc_godzin = intval(validate_creds($_POST['punkt_godzina_koniec'])) - intval(validate_creds($_POST['punkt_godzina_start']));
    
            $id_user = UserSession::getUserId();
            $id_puntku;
    
            //sprawdzenie czy istnieje taki adres w bazie danych
            $sql = "SELECT * FROM baza_sklepow WHERE adres_sklepu = :adres_sklepu";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':adres_sklepu', $adres_puntku);
            $stmt -> execute();
    
            if ($stmt->rowCount() === 1)  //jesli istnieje taki adres
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $id_puntku = $row['id_sklepu'];
                $stara_ilosc_godzin = $row['suma_godzin'];
    
                //sprawdzenie czy istnieje juz akcja dla tego usera tego dnia
                $sql = "SELECT * FROM akcje WHERE data = :data AND id_user = :id_user";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':data', $data_akcji);
                $stmt -> bindParam(':id_user', $id_user);
                $stmt -> execute();
    
                if ($stmt->rowCount() === 1) //jesli istnieja to sie jedynie zaaktualizuje adres wpisany oraz godziny
                {
                    try
                    {
                        $sql = "UPDATE akcje SET id_sklepu = :id_sklepu, start_godzina = :start_godzina, koniec_godzina = :koniec_godzina WHERE id_user = :id_user AND data = :data";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT); // Added PARAM_INT if $id_user is an integer
                        $stmt->bindParam(':id_sklepu', $id_puntku, PDO::PARAM_INT); // Added PARAM_INT if $id_sklepu is an integer
                        $stmt->bindParam(':data', $data_akcji); // If $data_akcji is a string or date
                        $stmt->bindParam(':start_godzina', $start_akcji_godzina); // Make sure this is a valid time format
                        $stmt->bindParam(':koniec_godzina', $koniec_akcji_godzina); // Same here for valid time format
                        $stmt->execute();
    
                        //no i zostaje wyrowannie godzin
                        $nowa_ilosc_godizn = $stara_ilosc_godzin + $ilosc_godzin;
                        $sql = "UPDATE baza_sklepow SET suma_godzin = :ilosc_godzin WHERE id_sklepu = :id_sklepu";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':ilosc_godzin', $nowa_ilosc_godzin, PDO::PARAM_INT); // Added PARAM_INT
                        $stmt->bindParam(':id_sklepu', $id_puntku, PDO::PARAM_INT); // Added PARAM_INT
                        $stmt->execute();
    
                        header("Location: ../../calendar_editor.php");
                        exit();
                    }
                    catch(Exception $e)
                    {
                        echo $e;
                    }
                }
                else
                {
                    try
                    {
                        $nowa_ilosc_godizn = $stara_ilosc_godzin + $ilosc_godzin;
        
                        $sql = "UPDATE baza_sklepow SET suma_godzin = :ilosc_godzin WHERE id_sklepu = :id_sklepu";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':ilosc_godzin', $nowa_ilosc_godzin, PDO::PARAM_INT); // Added PARAM_INT
                        $stmt->bindParam(':id_sklepu', $id_puntku, PDO::PARAM_INT); // Added PARAM_INT
                        $stmt->execute();
                        
                                        // Insert new action into the `akcje` table
                        $sql = "INSERT INTO `akcje` (`id_user`, `id_sklepu`, `data`, `start_godzina`, `koniec_godzina`) 
                                VALUES (:id_user, :id_sklepu, :data, :start_godzina, :koniec_godzina)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT); // Added PARAM_INT if $id_user is an integer
                        $stmt->bindParam(':id_sklepu', $id_puntku, PDO::PARAM_INT); // Added PARAM_INT if $id_sklepu is an integer
                        $stmt->bindParam(':data', $data_akcji); // If $data_akcji is a string or date
                        $stmt->bindParam(':start_godzina', $start_akcji_godzina); // Make sure this is a valid time format
                        $stmt->bindParam(':koniec_godzina', $koniec_akcji_godzina); // Same here for valid time format
                        $stmt->execute();
        
                        header("Location: ../../calendar_editor.php");
                        exit();
                    }
                    catch(Exception $e)
                    {
                        echo $e;
                    }
                }
            }
            else
            {
                header("Location: ../../calendar_editor.php?error=Zly adres");
                exit();
            }
        }
    }
    if($_POST['submit'] === "Usun") //usuwanie event
    {
        $adres_puntku = validate_creds($_POST['lokalizacja_punkt']);
        $data_akcji = validate_creds($_POST['date']);

        $id_user = UserSession::getUserId();

        //sprawdzenie czy istnieje juz akcja dla tego usera tego dnia
        $sql = "SELECT * FROM akcje WHERE data = :data AND id_user = :id_user";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':data', $data_akcji);
        $stmt -> bindParam(':id_user', $id_user);
        $stmt -> execute();

        if($stmt->rowCount() === 1)
        {
            $sql = "DELETE FROM akcje WHERE data = :data AND id_user = :id_user";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':data', $data_akcji);
            $stmt -> bindParam(':id_user', $id_user);
            $stmt -> execute();
            header("Location: ../../calendar_editor.php");
            exit();
        }
        else
        {
            header("Location: ../../calendar_editor.php?error=Probujesz usunac cos co nie istnieje");
            exit();
        }
    }


}
else
{
    header("Location: ../../calendar_editor.php");
    exit();
}

?>