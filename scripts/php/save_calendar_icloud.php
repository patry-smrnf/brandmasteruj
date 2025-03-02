<?php
    include('database.php');
    include('security.php');
    include('config.php');
    require_once('user_session.php');

    is_logged();

    $id_user = UserSession::getUserId();

    $sql = "SELECT * FROM akcje WHERE id_user = :id_user";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':id_user', $id_user);
    $stmt -> execute();

    //zgranie calej bazy akcji dotyczacej usera
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //icloud stuff tutaj
    $ical_content = "BEGIN:VCALENDAR\r\n";
    $ical_content .= "VERSION:2.0\r\n";
    $ical_content .= "PRODID:-//Brandmasterujsb//NONSGML v1.0//EN\r\n";

    var_dump($records);
    foreach($records as $event)
    {
        //branie adresu akcji
        $id_sklepu = $event['id_sklepu'];
        $sql = "SELECT * FROM baza_sklepow WHERE id_sklepu = :id_sklepu";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id_sklepu', $id_sklepu);
        $stmt -> execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $adres_akcji = $row['adres_sklepu'];

        //przypisywanie podstawowych danych dla wydarzenia
        $event_title = htmlspecialchars("Brandmasterowanie");
        $event_description = htmlspecialchars($adres_akcji);
        $event_location = htmlspecialchars($adres_akcji);

        //sprecyzowanie godzin w ktorych to bedzie i dzien
        $start_datetime = $event['data'] . ' ' . $event['start_godzina'];
        $end_datetime = $event['data'] . ' ' . $event['koniec_godzina'];
        
        //konwertowanie do formatu icloud daty i godziny
        $start_date = date("Ymd\THis", strtotime($start_datetime));
        $end_date = date("Ymd\THis", strtotime($end_datetime));

        //
        $ical_content .= "BEGIN:VEVENT\r\n";
        $ical_content .= "UID:" . uniqid() . "@brandmasteruj.pzdr\r\n";
        $ical_content .= "DTSTAMP:" . date("Ymd\THis") . "Z\r\n";
        $ical_content .= "DTSTART:$start_date\r\n";
        $ical_content .= "DTEND:$end_date\r\n";
        $ical_content .= "SUMMARY:$event_title\r\n";
        $ical_content .= "DESCRIPTION:$event_description\r\n";
        $ical_content .= "LOCATION:$event_location\r\n";
        $ical_content .= "END:VEVENT\r\n";
    }

    $ical_content .= "END:VCALENDAR\r\n";

    $file_path = "multiple_events.ics";
    file_put_contents($file_path, $ical_content);
    
    // Redirect to the ICS file to trigger automatic opening in iCloud Calendar
    header("Location: $file_path");
    exit;
?>