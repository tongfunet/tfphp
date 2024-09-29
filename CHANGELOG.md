# Release Notes

## [v0.1.0] - 2024/4/8

* [0.1.0] This is the first version of the TFPHP framework, which currently only implements the basic routing function of the framework.

## [v0.2.0] - 2024/6/6

* [0.2.0] We have added basic controllers for various request resources on top of the first version, including APIs, web pages, and RESTful style APIs.
* [0.2.0] We have designed a tfview template controller that can implement basic web page template functions. tfview uses precompiled mode to load web pages, which can significantly improve the loading speed after the first loading of web pages.

## [v0.5.0] - 2024/7/9

* [0.3.0] Added class tfdo for implementing various operations on relational databases.
* [0.4.0] Added tfdao class group for implementing basic data CRUD operations, tfdaoOneToOne class for implementing one-to-one data association relationships, tfdaoOneToMany class for implementing one to many data association relationships, and tfdaoAnyToMany class for implementing many to many data association relationships.
* [0.5.0] Added tfmodel class to define the data model, and implemented the processing of model data through various pre-defined tfdao classes.

## [v0.5.8] - 2024/8/1

* [0.5.2] Added the built-in tfphp tool 'build_mase_madels_by_datasource' to build the basic model of the tfphp framework through MySQL database connection.
* [0.5.3] Added support for include functionality to the tfview template controller, which can be used to include files within the template; The tfview template controller has added support for the 'for/if/elseif/else' syntax.
* [0.5.5] Added a set of xxx2 methods to the tfdo class, supporting passing parameters with '?'; Added a set of xxx3 methods to the tfdo class, supporting passing parameters as '@int/str'; The tfdo class supports the charset parameter to control database encoding.
* [0.5.6] TFPHP routing has added the 'getStaticRoutes' static routing method and 'getRERoutes' regular expression routing method.
* [0.5.7] The class 'tfview' has added support for 'if/elseif/else' conditional statements, 'include include' statements, and 'for in/for in range' loops.
* [0.5.8] The class 'tfdaoOneToMany' has added a 'select' method, and the class 'tfdaoAnyToMany' has added a 'select' method.

## [v0.6.0] - 2024/8/28

* [v0.6.0] TFPHP has added a location method and applied it to three basic classes: tfapi, tfpage, and tfrestfulAPI.
* [v0.6.0] TFPHP stores the 'HTTP_SAW_POST_DATA' data as global data in $_POST.
* [v0.6.0] Added the built-in tfphp tool 'static' to directly respond to requests for static resources.

## [v0.6.1] - 2024/9/9

* [v0.6.1] Added class tfimage for processing image scaling, cropping, and other operations.

## [v0.6.2] - 2024/9/25

* [v0.6.2] Transfer all response functions from tfphp to tfresponse.
* [v0.6.2] Added a readiness test function to the tfdo class to enable database connections when needed, thereby reducing resource usage.
* [v0.6.2] Added the fillColor method to the tfimage class to fill the image with color.
* [v0.6.2] Added the write Text method to the tfimage class to add text to the image.
* [v0.6.2] Added class tfAES for encryption and decryption of AES algorithm.
* [v0.6.2] Added a "resource" template tag to the tfview class to load script files, style sheet files, and so on.
* [v0.6.2] Added class tfredis for implementing various operations on Redis databases.
* [v0.6.2] Added the addWatermark method to the tfimage class to add watermarks to the image.
* [v0.6.2] Added getObject/setObject/export/delete methods to the tfredis class for manipulating structured data.
