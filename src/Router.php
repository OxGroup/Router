<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 05.12.2015
 * Time: 19:56
 */
namespace Ox\Router;
class Router
{
    private static $middlewareFilters = array();
    public static $route;
    public static $controller;
    public static $statusCode;
    public static $routeCounts=0;
    /**
     * @param $rout
     *
     * @return AppRoute
     */
    public static function rout($rout)
    {
        return new AppRoute($rout);
    }

    /**
     * @param       $name
     * @param array $groups
     *
     * @return $this
     */
    public static function addMiddlewareGroup($name, array $groups, array $filtersMiddleware = array())
    {
        if (!empty($filtersMiddleware)) {
            self::$middlewareFilters[$name] = $filtersMiddleware;
        }
        RouteMiddleware::$middleware[$name] = $groups;
    }


    /**
     * @param          $name
     * @param \Closure $function
     *
     * @return bool
     */
    public static function setMiddlewareGroup($name, \Closure $function)
    {
        RouteMiddleware::$nameGroup = "";
        $middlewareGroup = new RouteMiddleware();
        $middlewareGroup->class = true;
        $result = $middlewareGroup->setMiddlewareGroup($name);
        if ($result->middlewareNext == true) {
            if (!empty(self::$middlewareFilters)) {
                RouteMiddleware::$nameGroup = $name;
                RouteMiddleware::$middlewareFilters = self::$middlewareFilters;
            }
            $middlewareGroup->class = false;
            $data = $function(); // отложенное выполнение кода
            return $data;
        }
        return false;
    }
}
