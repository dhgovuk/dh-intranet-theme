<?php

$registrar = require __DIR__.'/../app/load.php';
$registrar->register();

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
require __DIR__.'/../lib/core.php';
require __DIR__.'/../lib/post-types.php';
require __DIR__.'/../lib/menus.php';
require __DIR__.'/../lib/widgets.php';
require __DIR__.'/../lib/acf.php';
require __DIR__.'/../lib/locations.php';
require __DIR__.'/../lib/ajax-logged-in-check.php';
require __DIR__.'/../lib/wp-admin.php';
require __DIR__.'/../lib/remove-meta-boxes.php';
require __DIR__.'/../lib/visual-form-builder-custom.php';
require __DIR__.'/../lib/taxonomies.php';
require __DIR__.'/../lib/etc.php';
require __DIR__.'/../lib/comment.php';
require __DIR__.'/../lib/tasks.php';
require __DIR__.'/../lib/event-listings.php';
require __DIR__.'/../lib/helpers.php';
require __DIR__.'/../lib/it-updates.php';
require __DIR__.'/../lib/options-settings.php';
