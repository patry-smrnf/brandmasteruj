<?php
class UserSession
{
    private static ?string $user_id = null;
    private static ?string $user_login = null;
    private static ?string $user_token = null;

    public static function init(string $user_id, string $user_login, string $user_token): void
    {
        self::$user_id = $user_id;
        self::$user_login = $user_login;
        self::$user_token = $user_token;
    }

    public static function getUserId(): ?string
    {
        return self::$user_id;
    }

    public static function getUserLogin(): ?string
    {
        return self::$user_login;
    }

    public static function getUserToken(): ?string
    {
        return self::$user_token;
    }
}

?>