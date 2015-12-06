<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 05.12.2015
 * Time: 21:05
 */

namespace Ox\Router;

class RouteMiddleware
{
    protected $middlewareNext = true;
    private $route, $class, $method;
    public static $middleware = array();

    /**
     * RouteMiddleware constructor.
     *
     * @param bool $route
     * @param bool $class
     * @param bool $method
     */
    public function __construct($route = false, $class = false, $method = false)
    {
        $this->route = $route;
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * @param        $route
     * @param        $class
     * @param string $method
     */
    private function fileController($route, $class, $method = "")
    {

        $file = "../http/controllers/" . $class . "Controller.php";
        $file = str_replace("\\", "/", $file);
        if (is_readable($file) == false) {
            die ($file . ' Controller Not Found');
        } else {
            $class .= "Controller";
            try {
                $class = "\\OxApp\\controllers\\" . $class;

                $controller = new  $class();
                if (is_subclass_of($controller, 'Ox\App')) {
                    if (!empty($this->ContentType))
                        header('Content-Type: ' . $this->ContentType);


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
                    die ('No extends App');
                }
            } catch (\Exception $e) {
                echo "ERROR: $e";
            }
        }
    }


    /**
     * @param       $middlewareName
     * @param array $rules
     *
     * @return $this
     */
    public function middleware($middlewareName, $rules = array())
    {
        if ($this->middlewareNext == true and $this->class !== false) {
            try {
                $class = "\\OxApp\\middleware\\" . $middlewareName;
                $controller = new  $class();
                $this->middlewareNext = $controller->rules($rules);
            } catch (\Exception $e) {
                echo "ERROR: $e";
            }
        }
        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setMiddlewareGroup($name)
    {
        if (!empty(self::$middleware[$name])) {
            foreach (self::$middleware[$name] as $name => $rule) {
                $this->middleware($name, $rule);
            }
        }
        return $this;
    }


    /**
     * @return $this
     */
    public function go()
    {
        if ($this->middlewareNext == true and $this->class !== false) {
            $this->fileController($this->route, $this->class, $this->method);
        }
        return $this;
    }

}