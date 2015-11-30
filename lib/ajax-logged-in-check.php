<?php

add_action('wp_ajax_logged-in-check', 'logged_in_check');
add_action('wp_ajax_nopriv_logged-in-check', 'logged_in_check');
function logged_in_check()
{
    $returnArray = array();
    $returnArray['logged-in'] = is_user_logged_in() ? 'true' : 'false';
    if ((current_user_can('edit_pages'))) {
        $returnArray['editor-suite'] = is_user_logged_in() ? 'true' : 'false';
    }
    echo json_encode($returnArray);
    die();
}
