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
    public function rout($rout)
    {
        return new AppRoute($rout);
    }

    public function addGroupMiddleware($name, array $groups)
    {
        RouteMiddleware::$middleware[$name] = $groups;
    }
}