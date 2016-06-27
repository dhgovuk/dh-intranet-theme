<?php

namespace DHIntranet;

class LoginCookieDuration implements \Dxw\Iguana\Registerable
{
    public function register()
    {
        add_filter('auth_cookie_expiration', [$this, 'authCookieExpiration'], 50);
    }

    public function authCookieExpiration()
    {
        return YEAR_IN_SECONDS;
    }
}
