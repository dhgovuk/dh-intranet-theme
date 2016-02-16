<!DOCTYPE html>
<!--[if IE 7]><html class="no-js lt-ie10 lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie10 lt-ie9 " <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]><html class="no-js lt-ie10" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title><?php wp_title('|', true, 'right'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="dns-prefetch" href="//platform.twitter.com">
  <link rel="dns-prefetch" href="//syndication.twitter.com">
  <link rel="dns-prefetch" href="//cdn.syndication.twimg.com">
  <link rel="dns-prefetch" href="//google-analytics.com">

  <script src="<?php echo get_template_directory_uri(); ?>/../build/js/modernizr.min.js"></script>

  <?php wp_head(); ?>

  <link rel="shortcut icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/../assets/img/favicon.ico">
  <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/../assets/img/touch-icon.png">
  <link rel="apple-touch-icon-precomposed" href="<?php echo get_template_directory_uri(); ?>/../assets/img/touch-icon.png">

</head>
