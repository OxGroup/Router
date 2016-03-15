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
            Router::$statusCode = "404";
            $this->sandResponseCode(Response::HTTP_METHOD_NOT_ALLOWED);
            throw new \Exception($file . ' Controller Not Found');
        } else {
            $class .= "Controller";
            try {
                $this->useMethod($class, $method);
                Router::$route = $route;
                Router::$controller = $class;
                Router::$routeCounts += 1;
            } catch (\RuntimeException $e) {
                $this->sandResponseCode(Response::HTTP_BAD_GATEWAY);
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
        $controller = new  $class();
        if (is_subclass_of($controller, 'Ox\App')) {
            if (!empty($method)) {
                $this->tryRunMethod($controller, $method);
            } else {
                $request = Request::createFromGlobals();
                $this->tryRunMethod($controller, strtolower($request->server->get("REQUEST_METHOD")));
            }
            $this->sandResponseCode(Response::HTTP_OK);
        } else {
            $this->sandResponseCode(Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * @param $controller
     * @param $method
     *
     * @throws \Exception
     */
    protected function tryRunMethod($controller, $method)
    {
        try {
            $controller->$method();
        } catch (\Exception $e) {
            $this->sandResponseCode(Response::HTTP_METHOD_NOT_ALLOWED);
            throw new \Exception($e);
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
