<?php
/*! DevA 2 - github.com/mignz/DevA2 */

namespace DevA2;

use Phalcon\Support\Debug;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Simple as View;

/**
 * Phalcon Debugger
 */
(new Debug())->listen();

/**
 * Phalcon autoloader
 */
require __DIR__ . '/../app/loader.php';

/**
 * Container
 */
$container = new FactoryDefault();

/**
 * Load the view service with volt
 */
$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(__DIR__ . '/../app/views/');
        return $view;
    }
);

/**
 * Instantiate the Micro application
 */
$app = new Micro($container);

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
    function () use ($app) {
        echo $app['view']->render('errors/woops');
    }
);

/**
 * Start the app
 */
$app->handle(
    $_SERVER['REQUEST_URI']
);
