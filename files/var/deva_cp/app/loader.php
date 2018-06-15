<?php
/*! DevA 2 - github.com/mignz/DevA2 */

use Phalcon\Loader;

$loader = new Loader();
$loader->registerNamespaces(
    [
       'DevA2\Helpers' => __DIR__ . '/helpers/'
    ]
);
$loader->register();
