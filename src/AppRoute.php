<?php
/**
 * Created by OxGroup.
 * User: Александр
 * Date: 05.12.2015
 * Time: 20:52
 */
namespace Ox\Router;

/**
 * Class AppRoute
 *
 * @package Ox\Router
 */
class AppRoute
{
    protected $method = "ALL";
    protected $route;
    protected $methodRoute = false;
    protected $classRoute;
    
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
        $this->classRoute = Router::$defaultNameSpace . $app;
        
        $request = Router::$requestDriver;
        if (!empty($request)) {
            if (!$request->query->get("q")) {
                $request->query->set("q", "/");
            }
        } else {
            $_GET['q'] = "/";
        }
        
        if ($this->method === "ALL" ||
            ((!empty($request) && $this->method === $request->server->get("REQUEST_METHOD")) ||
                $_SERVER['REQUEST_METHOD'] === $this->method)
        ) {
            $this->useRoute($this->route);
        } else {
            $this->classRoute = false;
        }
        
        return new RouteMiddleware($this->route, $this->classRoute, $this->methodRoute);
    }
    
    /**
     * @param $route
     */
    protected function useRoute($route)
    {
        $request = Router::$requestDriver;

        $this->method = $request->server->get("REQUEST_METHOD");
        $get = $request->server->get("REQUEST_URI");

        $check = explode("?", $get);
        if (isset($check[1])) {
            $get = $check[0];
        }
        $get = Helper::fixStandardRoute($get);
        
        $setGet = array();
        $setGetRoutes = explode("/", $route);
        if (0 !== count($setGetRoutes)) {
            $getResut = explode("/", $get);
            $countGet = 0;
            foreach ($setGetRoutes as $rout) {
                //print_r($rout);
                $testRoute = explode("=>", $rout);
                if (!empty($testRoute[1]) && isset($getResut[$countGet])) {
                    $setGet[$testRoute[1]] = $getResut[$countGet];
                    $route = str_replace("{$testRoute[0]}=>$testRoute[1]", "$testRoute[0]", $route);
                }
                $countGet++;
            }
        }
        $routePreg = Helper::getMacrosMatch($route);
        //print_r($setGet);
        if ((preg_match($routePreg, $get) && $route !== $get) || $route === $get) {
            $this->readyRout($setGet);
        } else {
            $this->classRoute = false;
        }
    }
    
    /**
     * @param $setGet
     */
    protected function readyRout($setGet)
    {
        $request = Router::$requestDriver;
        
        if (0 !== count($setGet)) {
            if (!empty($request)) {
                foreach ($setGet as $keyGet => $valGet) {
                    $request->query->set($keyGet, $valGet);
                }
            }
            $this->addGlobalRequest($setGet);
        }
        $resultRoute = explode("::", $this->classRoute);
        if (!empty($resultRoute[1])) {
            $this->methodRoute = $resultRoute[1];
            $this->classRoute = $resultRoute[0];
        }
    }
    
    /**
     * @param $setArray
     */
    protected function addGlobalRequest($setArray)
    {
        $_GET = $setArray + $_GET;
        $_REQUEST = $setArray + $_REQUEST;
    }
}
