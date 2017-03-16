<?php // Adds Additional ACF Options pages

if(function_exists("register_options_page") && current_user_can('edit_users'))
{
  register_options_page('Homepage');
  register_options_page('Policy Kit');
}
