<?php

function fetchGet($url, $headers = [], $useCookies = true) 
{
    $ch = curl_init($url);

    // Extract user cookies if needed
    if ($useCookies) {
        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            $cookies[] = "$key=$value";
        }
        $cookieString = implode("; ", $cookies);
        $headers[] = "Cookie: $cookieString"; // Add cookies to headers
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification (if necessary)
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set headers if provided

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["error" => $error];
    }

    curl_close($ch);

    return json_decode($response, true);
}
function sendPost($url, $data) {
    $jsonData = json_encode($data); // Convert array to JSON

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',  // Tell the API we are sending JSON
        'Content-Length: ' . strlen($jsonData)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}


?>