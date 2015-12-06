<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 05.12.2015
 * Time: 20:52
 */

namespace Ox\Router;

use Symfony\Component\HttpFoundation\Request;

class AppRoute
{
    protected $method = "ALL";
    protected $route;

    /**
     * AppRoute constructor.
     *
     * @param $route
     */
    public function __construct($route)
    {
        $this->route = $route;
    }

    /**
     * @param $method
     *
     * @return $this
     */
    public function method($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param $app
     *
     * @return RouteMiddleware
     */
    public function app($app)
    {
        $method = false;
        $class = $app;
        $route = $this->route;
        $request = new Request(
            $_GET,
            $_POST,
            $_COOKIE,
            $_SESSION,
            $_FILES,
            $_SERVER
        );
        if ($this->method === "ALL" or $this->method === $request->server->get("REQUEST_METHOD")) {
            if (!isset($_GET['q'])) {
                $_GET['q'] = "/";
            }

            if (!empty($_SERVER['REQUEST_URI'])) {
                $GET = $_SERVER['REQUEST_URI'];
            } else if (!empty($_SERVER['REDIRECT_URL'])) {
                $GET = $_SERVER['REDIRECT_URL'];
            } else {
                $GET = $_GET['q'];
            }

            $check = explode("?", $GET);
            if (isset($check[1])) {
                $GET = $check[0];
            }

            if (substr($GET, -1) !== "/") {
                $GET .= "/";
            }
            if ($GET{0} !== "/") {
                $GET = "/" . $GET;
            }
            if (substr($route, -1) !== "/") {
                $route .= "/";
            }
            if ($route{0} !== "/") {
                $route = "/" . $route;
            }

            $SetGet = array();
            $setGetRoutes = explode("/", $route);
            if (0 === count($setGetRoutes)) {
                $getResut = explode("/", $GET);
                $i = 0;
                foreach ($setGetRoutes as $rout) {
                    $testRoute = explode("=>", $rout);
                    if (!empty($testRoute[1]) and isset($getResut[$i])) {
                        $SetGet[$testRoute[1]] = $getResut[$i];
                        $route = str_replace("{$testRoute[0]}=>$testRoute[1]", "$testRoute[0]", $route);
                    }
                    $i++;
                }
            }
            $before = array(":num", ":char", ":charNum", ":text", ":img", "/",);
            $after = array("[0-9]*", "[A-Za-z]*", "[A-Za-z0-9-]*", "[A-Za-z0-9- .,:%+;]*", ".*[.](png|jpg|jpeg|gif)", '\/',);
            $routePreg = str_replace($before, $after, $route);
            $routePreg = "/^" . $routePreg . "$/i";
            if ((preg_match($routePreg, $GET) and $route != $GET) or $route == $GET) {
                if (0 === count($SetGet)) {
                    $_GET = $SetGet + $_GET;
                    $_REQUEST = $SetGet + $_REQUEST;
                }

                $resultRoute = explode("::", $class);
                if (!empty($resultRoute[1])) {
                    $class = $resultRoute[0];
                    $method = $resultRoute[1];
                }

            } else {
                $class = false;
            }
        } else {
            $class = false;
        }

        return new RouteMiddleware($route, $class, $method);
    }

}