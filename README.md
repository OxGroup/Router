# Router
  
  $route = new Router();
  
        $route->addGroupMiddleware("clientLocal", array(
            "Auth" => array("status" => "client"),
            "Domain" => array("hostname" => "localhost"),
            ),
            array("ToJson"=>array())
         );

        $route->rout("/login")->app("login")->go();

        Router::setMiddlewareGroup("clientLocal",function(){
             $route->rout("/")->app("index")->go();
             $route->rout("/order/:num=>id")->app("order")->go();
             $route->rout("/files/:img")->app("image")->go();
             $route->rout("/uploads/:img")->app("image")->middleware("Domain",array("hostname"=>"other.dev")->go();
        });
        
        

Controller:

DIR: http/controllers

Namespace: \OxApp\Controllers

NameController extends \Ox\App


Middleware:

DIR: http/middleware

Namespace - \OxApp\middleware

public function rules($rule=array())
