<?php


namespace Hobbyworld;


use Hobbyworld\Http\Request;
use Hobbyworld\Http\Router;

use Hobbyworld\Http\NotFoundException;
use Hobbyworld\Http\NotImplementedException;

use Hobbyworld\Database\Sqlite3Database;
use Hobbyworld\Grabber\HabrGrabber;

use Hobbyworld\Controller\AbstractController;
use Hobbyworld\Controller\AppController;
use Hobbyworld\Controller\ErrorController;

use Hobbyworld\Model\ArticlesModel;


class App {


    public static $config = null;

    private $router = null;


    public static function create (array $config) : self {

        self::$config = (object) $config;

        return new self ();
    }

	private function __construct () {

        $this->router = new Router ();
    }

    public function addRoutes (array $routes) {

        $this->router->addRoutes ($routes);

        return $this;
    }


	public function createController () : AbstractController {

        $request = new Request ();

        try {

            $route = $this->router->getRoute ($request);

            $db = new Sqlite3Database (self::$config->db_file, ArticlesModel::SCHEME);
            $grabber = new HabrGrabber ();

            $controller = AppController::factory ($request, $db, $grabber);
            $controller->setAction ($route->getAction ());
        }

        catch (NotImplementedException $ex) {

            $request->ex = $ex;
            $controller = new ErrorController ($request);
            $controller->setAction ('not_implemented');
        }

        catch (NotFoundException $ex) {

            $request->ex = $ex;
            $controller = new ErrorController ($request);
            $controller->setAction ('not_found');
        }

        catch (\Exception $ex) {

            $request->ex = $ex;
            $controller = new ErrorController ($request);
            $controller->setAction ('internal_server_error');
        }

        return $controller;
	}


    public static function autoload (string $className) {

        $namespace_length = strlen (__NAMESPACE__);

        if (strncmp (__NAMESPACE__, $className, $namespace_length) === 0) {

            if (file_exists ($filePath = str_replace (['/', '\\'], '/', self::$config->root_dir .  '/src' . substr ($className, $namespace_length)) . '.php')) {

                require $filePath;
            }
        }
    }
}