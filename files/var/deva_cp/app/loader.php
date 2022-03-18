<?php
/*! DevA 2 - github.com/mignz/DevA2 */

use Phalcon\Autoload\Loader;

$loader = new Loader();
$loader->setNamespaces(
    [
       'DevA2\Helpers' => __DIR__ . '/helpers/'
    ]
);
$loader->register();
