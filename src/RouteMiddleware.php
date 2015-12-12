<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 05.12.2015
 * Time: 21:05
 */
namespace Ox\Router;
class RouteMiddleware
{
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
    private function middleware($middlewareName, $rules = array())
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

            GoRoute::fileController($this->route, $this->class, $this->method);
            if (!empty(self::$nameGroup)) {
                $this->afterSetMiddlewareGroup(self::$nameGroup);
            }

        }
        return $this;
    }
}
