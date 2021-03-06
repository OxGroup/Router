<?php
/**
 * Created by OxGroup.
 * User: Александр
 * Date: 05.12.2015
 * Time: 19:56
 */
namespace Ox\Router;

/**
 * Class Router
 *
 * @package Ox\Router
 */
class Router
{
    private static $middlewareFilters = array();
    public static $route;
    public static $controller;
    public static $statusCode;
    public static $routeCounts = 0;
    public static $defaultRout = "";
    public static $defaultNameSpace = "";
    public static $requestDriver;
    
    /**
     * @param $rout
     *
     * @return AppRoute
     */
    public static function rout($rout)
    {
        $rout = Helper::fixStandardRoute($rout);
        
        return new AppRoute(self::$defaultRout . $rout);
    }
    
    /**
     * @param       $name
     * @param array $groups
     *
     * @return $this
     */
    public static function addMiddlewareGroup($name, array $groups, $filters = array(), $rout = "", $nameSpace = "")
    {
        if (0 !== count($filters)) {
            self::$middlewareFilters[$name] = $filters;
        }
        
        if (!empty($rout)) {
            self::$defaultRout = $rout;
        } else {
            self::$defaultRout = "";
        }
        
        if (!empty($nameSpace)) {
            self::$defaultNameSpace = $nameSpace;
        } else {
            self::$defaultNameSpace = "";
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
        if ($result->middlewareNext === true) {
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
