# Router
  
        Router::addGroupMiddleware("clientLocal", array(
            "Auth" => array("status" => "client"),
            "Domain" => array("hostname" => "localhost"),
            ),
            array("ToJson"=>array())
         );

        Router::rout("/login")->app("login")->save();

        Router::setMiddlewareGroup("clientLocal",function(){
              Router::rout("/")->app("index")->save();
              Router::rout("/order/:num=>id")->app("order")->save();
              Router::rout("/files/:img")->app("image")->save();
              Router::rout("/uploads/:img")->app("image")->middleware("Domain",array("hostname"=>"other.dev")->save();
        });
        
        

Controller:

DIR: OxApp/controllers

Namespace: \OxApp\Controllers

NameController extends \Ox\App


Middleware:

DIR: OxApp/middleware

Namespace - \OxApp\middleware

public function rules($rule=array())
