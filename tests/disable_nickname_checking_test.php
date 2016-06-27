<?php

class DisableNicknameCheckingTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \WP_Mock::setUp();
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testRegister()
    {
        $disableNicknameChecking = new \DHIntranet\DisableNicknameChecking();

        \WP_Mock::expectActionAdded('user_profile_update_errors', [$disableNicknameChecking, 'userProfileUpdateErrors']);

        $disableNicknameChecking->register();
    }

    public function testUserProfileUpdateErrors()
    {
        $disableNicknameChecking = new \DHIntranet\DisableNicknameChecking();

        $errors = 1;

        \WP_Mock::wpFunction('is_wp_error', [
            'with' => [1],
            'return' => false,
        ]);

        $disableNicknameChecking->userProfileUpdateErrors($errors);

        $this->assertEquals(1, $errors);
    }

    public function testUserProfileUpdateErrorsRemovesError()
    {
        $disableNicknameChecking = new \DHIntranet\DisableNicknameChecking();

        $errors = $this->getMockBuilder('WP_Error')->setMethods(['remove'])->getMock();

        $errors->expects($this->exactly(1))
        ->method('remove')
        ->with($this->identicalTo('nickname'));

        \WP_Mock::wpFunction('is_wp_error', [
            'with' => [$errors],
            'return' => true,
        ]);

        $disableNicknameChecking->userProfileUpdateErrors($errors);
    }
}
