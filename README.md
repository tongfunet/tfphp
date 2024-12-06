<h1 align="center">TFPHP v0.6.5</h1>

# TFPHP

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg)](http://www.php.net/)

TFPHP is a web application framework based on the PHP language. We believe that an excellent framework should first be able to achieve rapid development of web applications, secondly, the syntax should be very elegant, and finally, the code readability should be very high.

## Features

- Design based on PHP `7.4+`

The TFPHP framework is still undergoing continuous improvement. Interested friends, please continue to follow us.

## Document

<a href="https://tongfu.net/tag/TFPHP.html">Official Development Document</a>

## Install

The TFPHP framework utilizes the redirection function of web servers. Below we provide a configuration example using Nginx+FPM environment:

```apacheconf
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root           /var/www/html;
    index          index.html index.htm index.php;

    rewrite ^(.*)$ /index.php;
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

If you want to separate static and dynamic content, you can set it like this:

```apacheconf
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root           /var/www/html;
    index          index.html index.htm index.php;

    if ( $uri !~* (\/js\/|\/css\/|\/images\/|\/img3\/|\/ue\/|favicon.ico|robots.txt|sitemap.xml) ) {
        rewrite ^(.*)$ /index.php;
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

This is an example of a regular webpage, where the controller object extends the class tfphp\framework\system\tfpage:

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

This is an example of an API interface, where the controller object extension class tfphp\framework\system\tfapi:

```php
namespace tfphp\controller\api;

use tfphp\framework\system\tfapi;

class userState extends tfapi {
    protected function onLoad(){
        $this->responsePlaintextData("this is a demo for API /userState");
    }
}
```

This is an example of a RESTful style interface, where the controller object extends the tfphp\framework\system\tfrestfulAPI class:

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
        $this->responseJsonData($data);
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

The TFPHP framework is licensed under the <a href="https://opensource.org/licenses/MIT">MIT license</a>.
