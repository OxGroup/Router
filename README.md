# Router
  $route = new Router();

        $route->addGroupMiddleware("clientLocal", array(
            "Auth" => array("status" => "client"),
            "Domain" => array("hostname" => "localhost"),
        ));

        $route->rout("/login")->app("login")->go();
  
        $route->rout("/")->app("index")->setMiddlewareGroup("clientLocal")->go();
        $route->rout("/order/:num=>id")->app("order")->setMiddlewareGroup("clientLocal")->go();
        $route->rout("/files/:img")->app("image")->setMiddlewareGroup("clientLocal")->go();
        $route->rout("/uploads/:img")->app("image")->middleware("Domain",array("hostname"=>"other.dev")->go();
        

Controller:

DIR: http/controllers

Namespace: \OxApp\Controllers

NameController extends \Ox\App


Middleware:

DIR: http/middleware

Namespace - \OxApp\middleware

public function rules($rule=array())
