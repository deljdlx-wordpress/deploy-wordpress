<?php
require __DIR__ .'/static-vendor/autoload.php';
require_once(ABSPATH.'wp-admin/includes/user.php');


// nous stokons dans la constante OPROFILE_FILEPATH le chemin fichier correspondant à la racine du plugin
if(!defined('WOOF_FILEPATH')) {
    define('WOOF_FILEPATH', __DIR__);
}
