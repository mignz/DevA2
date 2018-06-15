<?php
/*! DevA 2 - github.com/mignz/DevA2 */

namespace DevA2;

use Phalcon\Flash\Direct;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View\Simple as View;

/**
 * Phalcon autoloader
 */
require __DIR__ . '/../app/loader.php';

/**
 * Instantiate the Micro application
 */
$app = new Micro();

/**
 * Load the view service with volt
 */
$app['volt'] = function ($view, $di) {
    $volt = new Volt($view, $di);
    $volt->setOptions(
        [
            'compiledPath'      => __DIR__ . '/../app/cache/volt/',
            'compiledExtension' => '.compiled',
            'compileAlways'     => false // True for development
        ]
    );
    return $volt;
};
$app['view'] = function () {
    $view = new View();
    $view->setViewsDir(__DIR__ . '/../app/views/');
    $view->registerEngines(
        [
            '.volt' => 'volt',
        ]
    );
    return $view;
};

/**
 * Load the app content
 */
require __DIR__ . '/../app/app.php';

/**
 * 404
 */
$app->notFound(
    function () use ($app) {
        echo $app['view']->render('errors/404');
    }
);

/**
 * Error handler
 */
$app->error(
    function ($exception) use ($app) {
        echo $app['view']->render('errors/woops');
    }
);

/**
 * Start the app
 */
$app->handle();
