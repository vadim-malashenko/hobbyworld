<?php


namespace Hobbyworld;


use Hobbyworld\Http\Request;
use Hobbyworld\Http\Response;
use Hobbyworld\Http\Router;
use Hobbyworld\Http\NotFoundException;


class App {


    private static $config = [
        'articles_per_page' => 0,
        'sqlite3_db_file' => ''
    ];


	public function __construct (array $routes, array $config) {


        $this->router = new Router ();
        $this->router->addRoutes ($routes);

        foreach ($config as $key => $value) {

            if (isset (self::$config [$key])) {

                self::$config [$key] = $value;
            }
        }

        $this->createResponse ()->send ();
    }


	public function createResponse () : Response {

        $request = new Request ();

        try {

            $route = $this->router->findRoute ($request);

            $request->setMatches($route->getMatches ());

            $className = __NAMESPACE__ . '\\Controller\\' . $route->getController ();
            $controller = new $className ($request);
            $action = $route->getAction();

            return $controller->$action ();
        }

        catch (NotFoundException $ex) {

            return new Response(404, $ex->getMessage());
        }

        catch (\Exception $ex) {

            return new Response (500, $ex->getMessage());
        }
	}


    public static function __callStatic ($key, $value) {

        return isset (self::$config [$key]) ? self::$config [$key] : null;
    }


    public static function autoload (string $className) {

        $namespace_length = strlen (__NAMESPACE__);

        if (strncmp (__NAMESPACE__, $className, $namespace_length) === 0) {

            if (file_exists ($filePath = str_replace (['/', '\\'], '/', HOBBYWORLD_APP_DIR . '/src' . substr ($className, $namespace_length)) . '.php')) {

                require $filePath;
            }
        }
    }
}