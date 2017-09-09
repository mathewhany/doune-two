K<?php

define('PHP_REQUIRED_VERSION', '5.4');

if (version_compare(PHP_VERSION, PHP_REQUIRED_VERSION, '<')) {
    exit('You must have PHP ' . PHP_REQUIRED_VERSION . ' or higher to run this app.');
}

use Bestawys\Theme\ThemeManager;
use Doune\Helpers\DouneView;
use Doune\Helpers\TemplateHelper;
use Doune\Html\FormBuilder;
use Doune\Html\HtmlBuilder;
use Doune\Session\SlimSessionManager;
use Doune\Url\SlimUrlGenerator;
use Doune\Auth\Auth;
use Doune\Helpers\Redirector;
use Illuminate\Database\Capsule\Manager as Capsule;
use Doune\Installer\Installer;
use Slim\Views\TwigExtension;

session_start();

if ( ! isset($rootDir)) {
    $rootDir = __DIR__ . '/..';
}

$paths = [
    'root'      => $rootDir,
    'public'    => $rootDir . '/public',
    'config'    => $rootDir . '/app/config.php',
    'templates' => $rootDir . '/templates',
    'cache'     => $rootDir . '/cache',
    'routes'    => $rootDir . '/app/routes'
];

require $rootDir . '/vendor/autoload.php';

$app = new \Slim\Slim(require $paths['config']);

$app->container['paths'] = $paths;

// Set the charset
$app->contentType('text/html; charset=utf-8');

// Register the database.
$app->container->singleton('db.capsule', function ($container) {
    
    $capsule = new Capsule;
    
    $capsule->addConnection(
        $container['settings']['db']
    );

    $capsule->setAsGlobal();

    $capsule->bootEloquent();

    return $capsule;
});

$app->container['db.capsule'];

// Register the installer.
$app->container->singleton('installer', function($container) {
    return new Installer(app());
});

// Register the form builder
$app->container->singleton('form', function ($container) {
    return new FormBuilder(new HtmlBuilder(), new SlimUrlGenerator(), new SlimSessionManager());
});

// Register auth class
$app->container->singleton('auth', function ($container) {
    return new Auth();
});

// Register the redirector helper
$app->container->singleton('redirector', function ($container) {
    return new Redirector(app());
});

$app->container->singleton('theme_manager', function ($container) {
    return new ThemeManager(
        $container['paths']['public'] . '/assets',
        $container['settings']['url'] . '/assets',
        'default',
        'default'
    );
});

// Configure Twig.
$view = $app->view(new Slim\Views\Twig());

$view->setTemplatesDirectory($app->paths['templates']);

$view->parserOptions = [
    // 'cache' => $app->paths['cache']
];

$view->parserExtensions = [
    new TwigExtension(),
    new TemplateHelper($app->container)
];

// Configure routes.
\Slim\Route::setDefaultConditions([
    'id' => '[0-9]+'
]);

require $app->paths['routes'] . '/home.php';

if ( ! $app->installer->isInstalled()) {
    $app->installer->install();
}

require $app->paths['routes'] . '/auth/login.php';
require $app->paths['routes'] . '/auth/check.php';
require $app->paths['routes'] . '/auth/logout.php';
require $app->paths['routes'] . '/auth/register.php';
require $app->paths['routes'] . '/auth/store.php';

require $app->paths['routes'] . '/settings/edit.php';
require $app->paths['routes'] . '/settings/update.php';

foreach (['articles'] as $resource) {
    foreach (['create', 'store', 'show', 'edit', 'update', 'destroy'] as $route) {
        require $app->paths['routes'] . '/' . $resource . '/' . $route . '.php';
    }
}

$app->container['settings'] = array_merge(
    $app->settings, Setting::lists('value', 'name')
);

$view->setData(['settings' => $app->settings]);

$app->theme_manager->setDefaultTheme($app->settings['default_theme']);

// Run the app
$app->run();