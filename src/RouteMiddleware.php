<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 05.12.2015
 * Time: 21:05
 */
namespace Ox\Router;

use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class RouteMiddleware
{

    public static $handlerFormat = "pretty";
    public $middlewareNext = true;
    public $class;
    public static $nameGroup;
    private $route, $method;
    public static $middleware = array();
    public static $middlewareFilters = array();

    /**
     * RouteMiddleware constructor.
     *
     * @param bool $route
     * @param bool $class
     * @param bool $method
     */
    public function __construct($route = false, $class = false, $method = false)
    {
        $this->route = $route;
        $this->class = $class;
        $this->method = $method;
    }


    /**
     * @param       $middlewareName
     * @param array $rules
     *
     * @return $this
     */
    public function middleware($middlewareName, $rules = array())
    {
        if ($this->middlewareNext == true and $this->class !== false) {
            try {
                $class = "\\OxApp\\middleware\\" . $middlewareName;
                $controller = new  $class();
                $this->middlewareNext = $controller->rules($rules);
            } catch (\Exception $e) {
                echo "ERROR: $e";
            }
        }
        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setMiddlewareGroup($name)
    {
        if (!empty(self::$middleware[$name])) {
            foreach (self::$middleware[$name] as $name => $rule) {
                $this->middleware($name, $rule);
            }
        }
        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function afterSetMiddlewareGroup($name)
    {
        if (!empty(self::$middlewareFilters[$name])) {
            foreach (self::$middlewareFilters[$name] as $name => $rule) {
                $this->middleware($name, $rule);
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function go()
    {
        if ($this->middlewareNext == true and $this->class !== false) {

            if (!empty(self::$nameGroup)) {
                $this->afterSetMiddlewareGroup(self::$nameGroup);
            }

            $whoops = new Run();
            if (self::$handlerFormat === "json") {
                $whoops->pushHandler(new JsonResponseHandler());
                header('Content-Type: application/json');

                self::$handlerFormat = "pretty";
            } else {
                $whoops->pushHandler(new PrettyPageHandler());
            }

            $whoops->pushHandler(function ($exception, $inspector, $run) {
                $inspector->getFrames()->map(function ($frame) {
                    if ($function = $frame->getFunction()) {
                        $frame->addComment("This frame is within function '$function'", 'cpt-obvious');
                    }
                    return $frame;
                });
            });

            $whoops->register();

            $goRoute = new GoRoute();
            $goRoute->fileController($this->route, $this->class, $this->method);
        }
        return true;
    }
}
