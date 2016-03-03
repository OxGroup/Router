<?php
/**
 * Created by OxGroup
 * User: Александр
 * Date: 05.12.2015
 * Time: 20:48
 */

namespace Ox\Router;

/**
 * Class RouteRules
 *
 * @package Ox\Router
 */
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
