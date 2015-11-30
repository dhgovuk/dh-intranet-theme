<?php

$title = wp_title('|', false, 'right');

ob_start();
include roots_template_path();
$body = ob_get_clean();

include __DIR__.'/base-error.php';
