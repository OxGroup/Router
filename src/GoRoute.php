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
     * @param        $route
     * @param        $class
     * @param string $method
     *
     * @throws \Exception
     */
    public function fileController($route, $class, $method = "")
    {
        $this->response = new Response();
        $file = "../OxApp/controllers/" . $class . "Controller.php";
        $file = str_replace("\\", "/", $file);
        if (is_readable($file) === false) {
            Router::$statusCode = "404";
            $this->sandResponseCore(Response::HTTP_METHOD_NOT_ALLOWED);
            throw new \Exception($file . ' Controller Not Found');
        } else {
            $class .= "Controller";
            try {
                $this->useMethod($class, $method);
                Router::$route = $route;
                Router::$controller = $class;
                Router::$routeCounts += 1;
            } catch (\RuntimeException $e) {
                $this->sandResponseCore(Response::HTTP_BAD_GATEWAY);
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
                try {
                    $controller->$method();
                } catch (\Exception $e) {
                    $this->sandResponseCore(Response::HTTP_BAD_GATEWAY);
                    Router::$statusCode = "418";
                    throw new \Exception($e);
                }
            } else {
                $this->switchMethod($controller);
            }
            $this->sandResponseCore(Response::HTTP_OK);
        } else {
            $this->sandResponseCore(Response::HTTP_METHOD_NOT_ALLOWED);
            Router::$statusCode = "418";
        }
    }

    /**
     * @param $controller
     *
     * @throws \Exception
     */
    protected function switchMethod($controller)
    {
        $request = Request::createFromGlobals();
        switch ($request->server->get("REQUEST_METHOD")) {
            case ("POST"):
                $this->tryRunMethod($controller, "post");
                break;
            case ("PUT"):
                $this->tryRunMethod($controller, "put");
                break;
            case ("UPDATE"):
                $this->tryRunMethod($controller, "update");
                break;
            case ("DELETE"):
                $this->tryRunMethod($controller, "delete");
                break;
            default:
                $this->tryRunMethod($controller, "view");
                break;
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
        } catch (\RuntimeException $e) {
            $this->sandResponseCore(Response::HTTP_BAD_GATEWAY);
            Router::$statusCode = "418";
            throw new \Exception($e);
        }
    }

    /**
     * @param $response
     */
    protected function sandResponseCore($response)
    {
        $this->response->setStatusCode($response);
        $this->response->send();
    }
}
