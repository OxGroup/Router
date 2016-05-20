<?php
/**
 * Created by OxGroup
 * User: Aliaxander
 * Date: 12.12.15
 * Time: 16:25
 */
namespace Ox\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Exception\ErrorException;

/**
 * Class GoRoute
 *
 * @package Ox\Router
 */
class GoRoute
{
    
    /**
     * @var array
     */
    public $response = array();
    
    /**
     * GoRoute constructor.
     */
    public function __construct()
    {
        $this->response = new Response();
    }
    
    /**
     * @param        $route
     * @param        $class
     * @param string $method
     *
     * @throws \Exception
     */
    public function fileController($route, $class, $method = "")
    {
        $file = "../OxApp/controllers/" . $class . "Controller.php";
        $file = str_replace("\\", "/", $file);
        if (is_readable($file) === false) {
            throw new \Exception($file . ' Controller Not Found');
        } else {
            $class .= "Controller";
            try {
                $this->useMethod($class, $method);
                Router::$route = $route;
                Router::$controller = $class;
                Router::$routeCounts += 1;
            } catch (\RuntimeException $e) {
                throw new \Exception($e);
            }
        }
    }
    
    /**
     * @param $class
     * @param $method
     *
     * @throws \Exception
     */
    protected function useMethod($class, $method)
    {
        $class = "\\OxApp\\controllers\\" . $class;
        try {
            $controller = new  $class();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
        if (is_subclass_of($controller, 'Ox\App')) {
            if (!empty($method)) {
                $result = $this->tryRunMethod($controller, $method);
            } else {
                $request = Router::$requestDriver;
                if (!empty($request)) {
                    $result = $this->tryRunMethod($controller, strtolower($request->server->get("REQUEST_METHOD")));
                } else {
                    $result = $this->tryRunMethod($controller, strtolower($_SERVER["REQUEST_METHOD"]));
                }
            }
            $response = new Response(
                $result,
                Response::HTTP_OK
            );
            $response->send();
        } else {
            $this->sandResponseCode(Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }
    
    /**
     * @param $controller
     * @param $method
     *
     * @return mixed
     * @throws \Exception
     */
    protected function tryRunMethod($controller, $method)
    {
        if ($method === "options") {
            $classMethods = get_class_methods($controller);
            $classMethodsResult = ["options"];
            foreach ($classMethods as $val) {
                if (!in_array($val, array("__construct", "__distruct"))) {
                    $classMethodsResult[] = $val;
                }
            }
            $acceptMethods = strtoupper(implode(",", $classMethodsResult));
            $response = new Response("", Response::HTTP_OK);
            $response->headers->set("Allow", $acceptMethods);
            $response->headers->set("access-control-allow-methods", $acceptMethods);
            $response->send();
        } else {
            try {
                return $controller->$method();
            } catch (\RuntimeException $e) {
                throw new \Exception($e);
            }
        }
    }
    
    /**
     * @param $response
     */
    protected function sandResponseCode($response)
    {
        $this->response->setStatusCode($response);
        $this->response->send();
    }
}
