<?php

namespace DHIntranet;

class DisableNicknameChecking implements \Dxw\Iguana\Registerable
{
    public function register()
    {
        add_action('user_profile_update_errors', [$this, 'userProfileUpdateErrors']);
    }

    public function userProfileUpdateErrors($errors)
    {
        if (is_wp_error($errors)) {
            $errors->remove('nickname');
        }
    }
}
