<?php

class LoginCookieDurationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \WP_Mock::setUp();

        if (!defined('YEAR_IN_SECONDS')) {
            define('YEAR_IN_SECONDS', 365 * 24 * 60 * 60);
        }
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testRegister()
    {
        \WP_Mock::expectFilterAdded('auth_cookie_expiration', ['\\DHIntranet\\LoginCookieDuration', 'authCookieExpiration'], 50);

        \DHIntranet\LoginCookieDuration::register();
    }

    public function testAuthCookieExpiration()
    {
        $output = \DHIntranet\LoginCookieDuration::authCookieExpiration(1);
        $this->assertEquals(365 * 24 * 60 * 60, $output);
    }
}
