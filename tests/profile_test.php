<?php

class Profile_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \WP_Mock::setUp();
        $this->profile = new \DHIntranet\Profile();
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testRegistrable()
    {
        $this->assertInstanceOf(\Dxw\Iguana\Registerable::class, $this->profile);
    }

    public function testRegister()
    {
        \WP_Mock::expectActionAdded('user_profile_update_errors', [$this->profile, 'validateProfile'], 10, 3);
        \WP_Mock::expectActionAdded('init', [$this->profile, 'profileRedirect']);
        \WP_Mock::expectActionAdded('profile_update', [$this->profile, 'profileUpdate']);
        \WP_Mock::expectActionAdded('login_head', 'wp_no_robots');

        \WP_Mock::expectFilterAdded('phpmailer_init', [$this->profile, 'homeEmailResetPassword']);

        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'feed_links', 2]]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'feed_links_extra', 3]]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'rsd_link']]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'wlwmanifest_link']]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'parent_post_rel_link', 10]]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'start_post_rel_link', 10]]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'adjacent_posts_rel_link_wp_head', 10]]);
        \WP_Mock::wpFunction('remove_action', ['times' => 1, 'args' => ['wp_head', 'rel_canonical']]);

        \WP_Mock::wpFunction('add_shortcode', ['times' => 1, 'args' => ['profile-page', array($this->profile, 'profileShortCode')]]);

        $this->profile->register();
    }

    public function testProfileRedirect()
    {
        global $pagenow;
        $pagenow = 'profile.php';
        \WP_Mock::wpFunction('is_user_logged_in', ['times' => 1, 'return' => true]);
        \WP_Mock::wpFunction('is_admin', ['times' => 1, 'return' => true]);
        \WP_Mock::wpFunction('wp_redirect', ['times' => 1, 'args' => ['/profile']]);

        $this->profile->profileRedirect();
    }

    public function testValidateProfileEmailNotValid()
    {
        $errors = $this->getMockBuilder('WP_Error')->setMethods(['add'])->getMock();
        $errors->expects($this->exactly(1))
               ->method('add');

        $user = $this->getUserObject();

        \WP_Mock::wpFunction('wp_get_current_user', ['times' => 1, 'return' => $user]);

        $this->getPostUser();
        $_POST['email'] = 'not_testdxw.com';

        $this->profile->validateProfile($errors);
    }

    public function testValidateProfileEmailNotInWhitelist()
    {
        $errors = $this->getMockBuilder('WP_Error')->setMethods(['add'])->getMock();
        $errors->expects($this->exactly(1))
               ->method('add');

        $user = $this->getUserObject();

        \WP_Mock::wpFunction('wp_get_current_user', ['times' => 1, 'return' => $user]);

        $this->getPostUser();
        $_POST['email'] = 'test@non-whitelist.com';

        $this->profile->validateProfile($errors);
    }

    public function testValidateProfilePasses()
    {
        $errors = $this->getMockBuilder('WP_Error')->setMethods(['add'])->getMock();
        $errors->expects($this->exactly(0))
               ->method('add');

        $user = $this->getUserObject();

        \WP_Mock::wpFunction('wp_get_current_user', ['times' => 1, 'return' => $user]);

        $this->getPostUser();
        $_POST['email'] = 'test1234@dxw.com';

        $this->profile->validateProfile($errors);
    }

    public function testHomeEmailResetPassword()
    {
        $_REQUEST['action'] = 'lostpassword';
        $phpMailer = $this->getMockBuilder('PHPMailer')->setMethods(['addAddress'])->getMock();
        $phpMailer->to = ['test'];
        $phpMailer->expects($this->exactly(0))
            ->method('addAddress');

        $user = $this->getUserObject();
        \WP_Mock::wpFunction('get_user_by', ['times' => 1, 'args' => ['email', 'user_email@dxw.com'], 'return' => $user]);
        \WP_Mock::wpFunction('get_user_meta', ['times' => 1, 'args' => [$user->ID, 'home_email', true], 'return' => 'home@email.com']);

        //  $this->profile->homeEmailResetPassword($phpMailer);
        $this->markTestIncomplete('Copied from `wtg-security`');
    }

    public function testProfileShortCode()
    {
        $this->markTestIncomplete('Copied from `wtg-security`');
    }

    public function testUpdateHomeEmail()
    {
        $this->markTestIncomplete('Copied from `wtg-security`');
    }

    public function testProfileUpdate()
    {
        $this->markTestIncomplete('Copied from `wtg-security`');
    }

    private function getPostUser()
    {
        $_POST = [
            'first_name' => 'Not test',
            'last_name' => 'Not demo',
            'action' => 'edituser'
        ];
    }

    /**
     * @return object
     */
    private function getUserObject()
    {
        $user = (object)[
            'ID' => 123,
            'first_name' => 'Test',
            'last_name' => 'Demo',
            'user_email' => 'test@dxw.com'
        ];
        return $user;
    }

    public function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}
