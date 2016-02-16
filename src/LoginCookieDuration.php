<?php

namespace DHIntranet;

class LoginCookieDuration
{
    public static function register()
    {
        add_filter('auth_cookie_expiration', ['\\DHIntranet\\LoginCookieDuration', 'authCookieExpiration'], 50);
    }

    public static function authCookieExpiration()
    {
        return YEAR_IN_SECONDS;
    }
}
