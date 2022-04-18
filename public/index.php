<?php

error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

class Application extends BaseApplication
{
    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    protected function registerServices()
    {

        $di = new FactoryDefault();

        $loader = new Loader();

        define('BASE_PATH', dirname(__DIR__));
        define('APP_PATH', BASE_PATH . '/apps');

        require BASE_PATH . '/vendor/autoload.php';


        /**
         * We're a registering a set of directories taken from the configuration file
         */
        $loader
            ->registerDirs([__DIR__ . '/../apps/library/'])
            ->registerNamespaces(
                [
                    'App\Components' => APP_PATH . '/components',
                    'App\Listeners' => APP_PATH . '/listeners'
                ]
            )
            ->register();

        // Registering a router
        $di->set('router', function () {

            $router = new Router();

            $router->setDefaultModule("frontend");

            $router->add('/:controller/:action', [
                'module'     => 'frontend',
                'controller' => 1,
                'action'     => 2,
            ])->setName('frontend');

            $router->add("/login", [
                'module'     => 'backend',
                'controller' => 'login',
                'action'     => 'index',
            ])->setName('backend-login');

            $router->add("/admin", [
                'module'     => 'backend',
                'controller' => 'index',
                'action'     => 'index',
            ])->setName('backend-product');

            $router->add("/admin/:controller/:action", [
                'module'     => 'backend',
                'controller' => 1,
                'action'     => 2,
            ])->setName('backend-product');

            $router->add("/admin/products", [
                'module'     => 'backend',
                'controller' => 'products',
                'action'     => 'index',
            ])->setName('backend-product');

            $router->add("/admin/orders", [
                'module'     => 'backend',
                'controller' => 'orders',
                'action'     => 'index',
            ])->setName('backend-product');

            $router->add("/admin/orders/add", [
                'module'     => 'backend',
                'controller' => 'orders',
                'action'     => 'add',
            ])->setName('backend-product');

            $router->add("/admin/products/delete/:params", [
                'module'     => 'backend',
                'controller' => 'products',
                'action'     => 'delete',
                'params'     => 3,
            ])->setName('backend-product');

            $router->add("/admin/products/edit/:params", [
                'module'     => 'backend',
                'controller' => 'products',
                'action'     => 'edit',
                'params'     => 3,
            ])->setName('backend-product');

            // $router->add("/admin/products/:action", [
            //     'module'     => 'backend',
            //     'controller' => 'products',
            //     'action'     => 1,
            // ])->setName('backend-product');

            $router->add("/products/:action", [
                'module'     => 'frontend',
                'controller' => 'products',
                'action'     => 1,
            ])->setName('frontend-product');

            return $router;
        });
        // setting translator
        $di->set(
            'translator',
            function () {
                $lang  = $this->getRequest()->getquery()['locale'] ?? 'en';
                $transComponentObject = new App\Components\LocaleComponent();
                return $transComponentObject->getTranslator($lang);
            }
        );
        // setting session #️⃣
        $di->set(
            'session',
            function () {
                $session = new Manager();
                $files = new Stream(
                    [
                        'savePath' => '/tmp'
                    ]
                );
                $session->setAdapter($files)->start();
                return $session;
            }
        );


        // setting mongo
        $di->set(
            'mongo',
            function () {
                $mongo = new \MongoDB\Client("mongodb://mongo4", array("username" => "root", "password" => "password123"));
                return $mongo->test;
            },
            true
        );

        $this->setDI($di);
    }

    public function main()
    {

        $this->registerServices();

        // Register the installed modules
        $this->registerModules([
            'frontend' => [
                'className' => 'Multiple\Frontend\Module',
                'path'      => '../apps/frontend/Module.php'
            ],
            'backend'  => [
                'className' => 'Multiple\Backend\Module',
                'path'      => '../apps/backend/Module.php'
            ]
        ]);
        $response = $this->handle($_SERVER['REQUEST_URI']);

        $response->send();
    }
}

$application = new Application();
$application->main();
