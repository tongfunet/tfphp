<h1 align="center">TFPHP v0.1.0</h1>

## TFPHP

TFPHP is a web application framework based on the PHP language. We believe that an excellent framework should first be able to achieve rapid development of web applications, secondly, the syntax should be very elegant, and the readability of the code should be very high.

## Learning TFPHP

The TFPHP framework is still being continuously improved. Interested friends, please continue to follow us.

The TFPHP framework utilizes the custom error page feature of web servers. Below, we provide a configuration example using the Nginx+FPM environment:

<pre>
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root           /var/www/html;
    index          index.html index.htm index.php;

    error_page 404 /index.php;
    location ~ \.php$ {
        client_body_buffer_size      1m;

        root           /var/www/html;
        fastcgi_pass   localhost:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
</pre>

## License

The TFPHP framework is open-sourced software licensed under the <a href="https://opensource.org/licenses/MIT">MIT license</a>.