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
        $loginCookieDuration = new \DHIntranet\LoginCookieDuration();

        \WP_Mock::expectFilterAdded('auth_cookie_expiration', [$loginCookieDuration, 'authCookieExpiration'], 50);

        $loginCookieDuration->register();
    }

    public function testAuthCookieExpiration()
    {
        $loginCookieDuration = new \DHIntranet\LoginCookieDuration();

        $output = $loginCookieDuration->authCookieExpiration(1);
        $this->assertEquals(365 * 24 * 60 * 60, $output);
    }
}
