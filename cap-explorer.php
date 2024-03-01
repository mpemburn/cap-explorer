<?php
/**
 * @package PSR standards-based plugin stub
 * @version 1.0.0
 */
/*
Plugin Name: Cap Explorer
Plugin URI:
Description: Display the capabilities of various roles per subsite
Author: Mark Pemburn (mpemburn@clarku.edu)
Version: 1.0.0
Author URI:
*/
namespace CapExplorer;

/* In order to use autoload, you'll need to add the namespace
to composer.json Example:,
{
  "autoload": {
    "psr-4": {
      "CapExplorer\\": "src/"
    }
  }
}

...then run composer install
*/
require_once __DIR__ . '/vendor/autoload.php';

use CapExplorer\AdminPage;

// Boot up classes as singletons here
AdminPage::boot();
