<?php

// You can't use WP functions here
// This is loaded by both base-404.php and ../../maintenance.php

require_once __DIR__.'/../vendor.phar';

if (!isset($title)) {
    $title = 'Error';
}

if (!isset($body)) {
    $body = '<h1>An error has occurred</h1>';
}

?>
<!doctype html>
<html>
    <head>
        <title><?php echo $title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <style>

            html {
                font-size: 16px;
                line-height: 1.6;
                font-family: -apple-system, '.SFNSText-Regular', 'San Francisco', Roboto, 'Segoe UI', 'Open Sans', 'Helvetica Neue', Arial, sans-serif;
                color: #313133;
                background: #fff;
            }

            h1 {
                font-size: 40px;
            }

            .container {
                max-width: 600px;
                margin: 0 auto;
            }

            .logo {
                text-align: center;
                margin: 30px 0;
            }

            .nav ul {
                margin: 0;
                padding: 0;
            }

            .nav li {
                float: left;
                margin-right: 15px;
                list-style-type: none;
            }

            .nav li:last-child {
                margin-right: 0;
            }

        </style>
    </head>
    <body>

        <div class="container">
            <div class="row logo">
                <img src="<?php h()->assetPath('img/department-of-health-logo.png') ?>">
            </div>

            <div class="row">
                <?php echo $body ?>
            </div>
        </div>

    </body>
</html>
