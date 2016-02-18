<?php
/**
 * Created by OxGroupMedia
 * User: Aliaxander
 * Date: 12.12.15
 * Time: 16:25
 */
namespace Ox\Router;

class GoRoute
{
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
            echo($file . ' Controller Not Found');
        } else {
            $class .= "Controller";
            try {
                $class = "\\OxApp\\controllers\\" . $class;
                Router::$route = $route;
                Router::$controller = $class;
                Router::$routeCounts += 1;
                $controller = new  $class();
                if (is_subclass_of($controller, 'Ox\App')) {
                    if (!empty($method)) {
                        try {
                            $controller->$method();
                        } catch (\Exception $e) {
                            Router::$statusCode = "418";
                            throw new \Exception($e);
                        }
                    } else {
                        if (!empty($_POST)) {
                            try {
                                $controller->post();
                            } catch (\RuntimeException $e) {
                                Router::$statusCode = "418";
                                throw new \Exception($e);
                            }
                        } else {
                            try {
                                $controller->view();
                            } catch (\RuntimeException $e) {
                                Router::$statusCode = "418";
                                throw new \Exception($e);
                            }
                        }
                    }
                    if (Router::$doubleRoute === false) {
                        throw new \Exception("Double route detect");
                    }
                } else {
                    Router::$statusCode = "418";
                    //echo ('No extends App');
                }
            } catch (\RuntimeException $e) {
                throw new \Exception($e);
            }
        }
    }
}
