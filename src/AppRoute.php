<?php
/**
 * Created by OxGroupMedia.
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
        $class = Router::$defaultNameSpace . $app;
        $route = $this->route;
        $request = new Request(
            $_GET,
            $_POST,
            $_SERVER
        );
        if ($this->method === "ALL" || $this->method === $request->server->get("REQUEST_METHOD")) {
            $this->method = $request->server->get("REQUEST_METHOD");

            if (!$request->query->get("q")) {
                //$_GET['q'] = "/";
                $request->query->set("q", "/");
            }

            if ($request->server->get("REQUEST_URI")) {
                $get = $request->server->get("REQUEST_URI");
            } elseif ($request->server->get("REDIRECT_URL")) {
                $get = $request->server->get("REDIRECT_URL");
            } else {
                $get = $request->query->get("q");
            }
            $check = explode("?", $get);
            if (isset($check[1])) {
                $get = $check[0];
            }
            if (substr($get, -1) !== "/") {
                $get .= "/";
            }
            if ($get{0} !== "/") {
                $get = "/" . $get;
            }
            if (substr($route, -1) !== "/") {
                $route .= "/";
            }
            if ($route{0} !== "/") {
                $route = "/" . $route;
            }
            $SetGet = array();
            $setGetRoutes = explode("/", $route);
            if (0 !== count($setGetRoutes)) {
                $getResut = explode("/", $get);
                $countGet = 0;
                foreach ($setGetRoutes as $rout) {
                    $testRoute = explode("=>", $rout);
                    if (!empty($testRoute[1]) && isset($getResut[$countGet])) {
                        $SetGet[$testRoute[1]] = $getResut[$countGet];
                        $route = str_replace("{$testRoute[0]}=>$testRoute[1]", "$testRoute[0]", $route);
                    }
                    $countGet++;
                }
            }
            $before = array(":num",
                ":char",
                ":charNum",
                ":text",
                ":img",
                "/",
                );
            $after = array(
                "[0-9]*",
                "[A-Za-z]*",
                "[A-Za-z0-9-]*",
                "[A-Za-z0-9- .,:%+;]*",
                ".*[.](png|jpg|jpeg|gif)",
                '\/',
                );
            $routePreg = str_replace($before, $after, $route);
            $routePreg = "/^" . $routePreg . "$/i";
            if ((preg_match($routePreg, $get) && $route !== $get) || $route === $get) {
                if (0 !== count($SetGet)) {
                    foreach ($SetGet as $keyGet => $valGet) {
                        $request->query->set($keyGet, $valGet);
                    }
                    $_GET = $SetGet + $_GET;
                    $_REQUEST = $SetGet + $_REQUEST;
                }
                $resultRoute = explode("::", $class);
                if (!empty($resultRoute[1])) {
                    $method = $resultRoute[1];
                    $class = $resultRoute[0];
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
