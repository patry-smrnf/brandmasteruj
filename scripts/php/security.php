<?php

function is_logged()
{
    $auth_cookie_name = "auth_token";
    if(isset($_COOKIE[$auth_cookie_name]) && validate_token())
    {
        $url = "http://localhost/brandmasteruj_newBackend/api/server/get_id.php";
        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            $cookies[] = "$key=$value";
        }
        $cookieString = implode("; ", $cookies);
    
        $ch = curl_init($url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cookie: $cookieString"]);
        $response = curl_exec(handle: $ch);
        if (curl_errno($ch)) 
        {
            return [
                "logged" => false
               ];
        } 
        else 
        {
            $data = json_decode($response, true);
            if(isset($data['Error']) && !empty($data['Error']))
            {
               return [
                "logged" => false
               ];
            }
            else
            {
                return [
                    "logged" => true,
                    "login" => $data['login'],
                    "id" => $data['id']
                   ];
            }
        }
    
    }
    echo"halo";

}

function validate_token()
{
    $url = "http://localhost/brandmasteruj_v2/api/user/check_token.php";
    $data = fetchGet($url);

    if($data['Success'] === "Poprawny Token")
    {
        return true;
    }

    return false;

}

function validate_creds($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>