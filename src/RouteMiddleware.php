<?php
/**
 * Created by OxGroupMedia
 * User: Александр
 * Date: 05.12.2015
 * Time: 21:05
 */
namespace Ox\Router;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class RouteMiddleware
{
    public static $debug = true;
    public static $handlerFormat = "pretty";
    public $middlewareNext = true;
    public $class;
    public static $nameGroup;
    private $route;
    private $method;
    public static $middleware = array();
    public static $middlewareFilters = array();

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
     * @param       $middlewareName
     * @param array $rules
     *
     * @return $this
     * @throws \Exception
     */
    public function middleware($middlewareName, array $rules = array())
    {
        if ($this->middlewareNext === true && $this->class !== false) {
            try {
                $class = "\\OxApp\\middleware\\" . $middlewareName;
                $controller = new  $class();
                $this->middlewareNext = $controller->rules($rules);
            } catch (\RuntimeException $e) {
                throw new \Exception($e);
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
     * @param $name
     *
     * @return $this
     */
    public function afterSetMiddlewareGroup($name)
    {
        if (!empty(self::$middlewareFilters[$name])) {
            foreach (self::$middlewareFilters[$name] as $name => $rule) {
                $this->middleware($name, $rule);
            }
        }

        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        if ($this->middlewareNext === true && $this->class !== false) {
            if (!empty(self::$nameGroup)) {
                $this->afterSetMiddlewareGroup(self::$nameGroup);
            }
            $whoops = new Run;
            $whoops->pushHandler(
                function ($exception, $whoops) {
                    // Remove any previous output
                    ob_get_level() && ob_end_clean();
                    // Set response code
                    http_response_code(500);
                    $logger = new Logger('errors');
                    $logger->pushHandler(new StreamHandler(__DIR__ . '/../../../../errors.log'));
                    $logger->critical(
                        $exception->getMessage(),
                        array(
                            'File' => $exception->getFile(),
                            'Line' => $exception->getLine()
                        )
                    );
                    // Display errors
                    if (self::$debug === true) {
                        assert_options(ASSERT_ACTIVE, true);
                        if (self::$handlerFormat === "json") {
                            $whoops->pushHandler(new JsonResponseHandler());
                            header('Content-Type: application/json');
                            self::$handlerFormat = "pretty";
                        } else {
                            $whoops->pushHandler(new PrettyPageHandler());
                        }
                        $whoops->handleException($exception);
                    } else {
                        Router::$statusCode = 505;
                        header("Location: /505");
                    }
                }
            );
            $whoops->register();
            $goRoute = new GoRoute();
            $goRoute->fileController($this->route, $this->class, $this->method);
        }

        return true;
    }
}
