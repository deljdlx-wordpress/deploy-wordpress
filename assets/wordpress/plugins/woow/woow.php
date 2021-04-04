<?php

/**
 * Plugin Name: Woow : the Woof Workbench plugin
 * Author: Julien Delsescaux
 */

use Woow\Plugin;

require __DIR__ .'/static-vendor/autoload.php';
require __DIR__ .'/../woof/autoload.php';


$plugin = Plugin::getInstance(__DIR__);
$plugin->register();

