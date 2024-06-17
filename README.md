<h1 align="center">TFPHP v0.2.0</h1>

## TFPHP

TFPHP is a web application framework based on the PHP language. We believe that an excellent framework should first be able to achieve rapid development of web applications, secondly, the syntax should be very elegant, and the readability of the code should be very high.

## Learning TFPHP

The TFPHP framework is still being continuously improved. Interested friends, please continue to follow us.

The TFPHP framework utilizes the redirection feature of web servers. Below, we provide a configuration example using Nginx+FPM environment:

```apacheconf
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root           /var/www/html;
    index          index.html index.htm index.php;

    rewrite ^(.*)$ /framework/tfphp.php;
    location ~ \.php$ {
        client_body_buffer_size      1m;

        root           /var/www/html;
        fastcgi_pass   localhost:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```

In this example, all requests will be redirected to the FPM server. If you want to separate static and dynamic content, you can set it like this:

```apacheconf
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root           /var/www/html;
    index          index.html index.htm index.php;

    if ($uri ~ !(\.(zip|rar|gz|bz|7z|jpg|jpeg|png|gif))$) {
        rewrite ^(.*)$ /framework/tfphp.php;
    }
    location ~ \.php$ {
        client_body_buffer_size      1m;

        root           /var/www/html;
        fastcgi_pass   localhost:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```

This is an example of a regular web page, where the controller object extends the class tfphp\framework\system\tfpage:

```php
namespace tfphp\controller;

use tfphp\framework\system\tfpage;

class index extends tfpage {
    protected function onLoad(){
        $this->view->setVar("title", "this is a page");
        $this->view->setVar("page", [
            "h1"=>"this is a page",
            "p"=>"it is a full HTML page.",
        ]);
    }
}
```

This is an example of an API interface, where the controller object extends the class tfphp\framework\system\tfapi:

```php
namespace tfphp\controller\api;

use tfphp\framework\system\tfapi;

class userState extends tfapi {
    protected function onLoad(){
        $this->responsePlaintextData("this is a demo for API /userState");
    }
}
```

This is an example of a RESTful style interface, where the controller object extends the class tfphp\framework\system\tfrestfulAPI:

```php
namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;

class user extends tfrestfulAPI {
    private function responseDemo(string $method){
        $data = [
            "METHOD"=>$method,
            "RESTFUL"=>[
                "RESOURCE"=>[
                    "NAME"=>$_SERVER["RESTFUL_RESOURCE_NAME"],
                    "VALUE"=>$_SERVER["RESTFUL_RESOURCE_VALUE"],
                    "FUNCTION"=>$_SERVER["RESTFUL_RESOURCE_FUNCTION"],
                ]
            ]
        ];
        $this->response($data);
    }
    protected function onGET(){
        $this->responseDemo("GET");
    }
    protected function onPOST(){
        $this->responseDemo("POST");
    }
    protected function onPUT(){
        $this->responseDemo("PUT");
    }
    protected function onDELETE(){
        $this->responseDemo("DELETE");
    }
}
```

## License

The TFPHP framework is open-sourced software licensed under the <a href="https://opensource.org/licenses/MIT">MIT license</a>.