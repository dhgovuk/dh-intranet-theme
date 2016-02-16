<?php

require __DIR__.'/../vendor.phar';

// Autoload
$loader = new \Aura\Autoload\Loader;
$loader->register();
$loader->addPrefix('DHIntranet', __DIR__.'/../src');

// Roots
roots_require('lib/utils.php');
roots_require('lib/init.php');
roots_require('lib/wrapper.php');
roots_require('lib/sidebar.php');
roots_require('lib/config.php');
roots_require('lib/titles.php');
roots_require('lib/cleanup.php');
roots_require('lib/gallery.php');
roots_require('lib/comments.php');
roots_require('lib/widgets.php');

// Non-roots
include __DIR__.'/../lib/feeds.php';
include __DIR__.'/../lib/assets.php';
include __DIR__.'/../lib/post-types.php';
include __DIR__.'/../lib/menus.php';
include __DIR__.'/../lib/widgets.php';
include __DIR__.'/../lib/pages.php';
include __DIR__.'/../lib/acf.php';
include __DIR__.'/../lib/locations.php';
include __DIR__.'/../lib/ajax-logged-in-check.php';
include __DIR__.'/../lib/wp-admin.php';
include __DIR__.'/../lib/remove-meta-boxes.php';
include __DIR__.'/../lib/visual-form-builder-custom.php';
include __DIR__.'/../lib/taxonomies.php';
include __DIR__.'/../lib/etc.php';
include __DIR__.'/../lib/comment.php';
include __DIR__.'/../lib/tasks.php';
include __DIR__.'/../lib/event-listings.php';
include __DIR__.'/../lib/helpers.php';
include __DIR__.'/../lib/it-updates.php';
include __DIR__.'/../lib/options-settings.php';

\DHIntranet\LoginCookieDuration::register();
