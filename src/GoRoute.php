<?php
/**
 * Created by PhpStorm.
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
     */
    public function fileController($route, $class, $method = "")
    {
        $file = "../apps/controllers/" . $class . "Controller.php";
        $file = str_replace("\\", "/", $file);
        if (is_readable($file) == false) {
            Router::$statusCode = "404";
            die ($file . ' Controller Not Found');
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
                            echo "ERROR: $e";
                        }
                    } else {
                        if (!empty($_POST)) {
                            try {
                                $controller->post();
                            } catch (\Exception $e) {
                                echo "ERROR: $e";
                            }
                        } else {
                            try {
                                $controller->view();
                            } catch (\Exception $e) {
                                echo "ERROR: $e";
                            }
                        }
                    }
                    die();
                } else {
                    Router::$statusCode = "418";
                    die ('No extends App');
                }
            } catch (\Exception $e) {
                echo "ERROR: $e";
            }
        }
    }
}