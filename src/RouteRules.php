<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 05.12.2015
 * Time: 20:48
 */

namespace Ox\Router;

class RouteRules
{
    /**
     * @param $appController
     *
     * @return $this
     */
    public function app($appController)
    {
        $this->appController = $appController;
        return $this;
    }
}
