**Current Build Status**

[![Build Status](http://ci.oxgroup.media/build-status/image/2)](http://ci.oxgroup.media/build-status/image/2)
[![Latest Stable Version](https://poser.pugx.org/ox/router/v/stable)](https://packagist.org/packages/ox/router)
[![Total Downloads](https://poser.pugx.org/ox/router/downloads)](https://packagist.org/packages/ox/router)
[![Latest Unstable Version](https://poser.pugx.org/ox/router/v/unstable)](https://packagist.org/packages/ox/router)
[![License](https://poser.pugx.org/ox/router/license)](https://packagist.org/packages/ox/router)

# Router
  
        Router::addGroupMiddleware("clientLocal", array(
            "Auth" => array("status" => "client"),
            "Domain" => array("hostname" => "localhost"),
            ),
            array("ToJson"=>array())
         );

        Router::rout("/login")->app("login")->save();
        Router::rout("/get")->method("GET")->app("login")->save();

        Router::setMiddlewareGroup("clientLocal",function(){
              Router::rout("/")->app("index")->save();
              Router::rout("/order/:num=>id")->app("order")->save();
              Router::rout("/files/:img")->app("image")->save();
              Router::rout("/uploads/:img")->app("image")->middleware("Domain",array("hostname"=>"other.dev")->save();
        });
        
        

Controller:

DIR: OxApp/controllers

Namespace: \OxApp\controllers

NameController extends \Ox\App


Middleware:

DIR: OxApp/middleware

Namespace - \OxApp\middleware

public function rules($rule=array())
