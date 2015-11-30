<?php

/*
* The custom taxonomy policy-tests is set by the user in the dashboard by an advanced custom fields tab box
* not threw the wordpress added-by-default taxonomy hence removing the default tax box to avoid confussion.
*/

function remove_policytests_tax_meta()
{
    remove_meta_box('policytestsdiv', 'policy-step', 'side');
}
add_action('admin_menu', 'remove_policytests_tax_meta');
