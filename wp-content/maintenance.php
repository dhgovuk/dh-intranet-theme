<?php

function esc_attr($t) {
    return htmlspecialchars($t);
}

function esc_html($t) {
    return htmlspecialchars($t);
}

require('plugins/dh-intranet/src/Shortcode.php');

$title = "We're having technical problems";

$links = (new \DHIntranetPlugin\Shortcode)->shortcode();

$body = "
<h1>$title</h1>

<p>We're working to get the Intranet back up as soon as possible.</p>

<p>You can still access DH's other tools, and call the IT Helpdesk on 020 3002 1019.</p>

$links
";

require('themes/dh-intranet/templates/base-error.php');
