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
     */
    public function addGroupMiddleware($name, array $groups)
    {
        RouteMiddleware::$middleware[$name] = $groups;
    }


    public static function group($name, \Closure $function)
    {

        $middlewareGroup = new RouteMiddleware();
        $result = $middlewareGroup->setMiddlewareGroup($name);
        var_dump($result->middlewareNext);
        if ($result->middlewareNext == true) {
            $data = $function(); // отложенное выполнение кода
            return $data;
        }
        return false;
    }

}