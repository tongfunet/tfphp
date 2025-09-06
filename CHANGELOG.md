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
* [v0.6.2] Added a 'resource' template tag to the tfview class to load script files, style sheet files, and so on.
* [v0.6.2] Added class tfredis for implementing various operations on Redis databases.

## [v0.6.3] - 2024/10/28

* [v0.6.2] Added the addWatermark method to the tfimage class to add watermarks to the image.
* [v0.6.2] Added getObject/setObject/export/delete methods to the tfredis class for manipulating structured data.
* [v0.6.3] Added constraint mode to the class 'tfdaoSingle', which greatly simplifies the function parameter format.
* [v0.6.3] Added constraint mode to the class 'tfdaoOneToOne', which greatly simplifies the function parameter format.
* [v0.6.3] Added constraint mode to the class 'tfdaoManyToMany', which greatly simplifies the function parameter format.
* [v0.6.3] Added class 'tfdaoBuilder' to create dao objects for data tables.
* [v0.6.3] Removed the built-in tool 'build_base_models_by_datasource' from 'tfphp' and used the class 'tfdaoBuilder' to dynamically create dao objects for data tables.
* [v0.6.3] The method 'getDAOSingle' of class 'tfmodel' has added a simplified method 'getSG', method 'getDAOOneToOne' has added a simplified method 'getO2O', and method 'getDAOManyToMany' has added a simplified method 'getM2M'.

## [v0.6.5] - 2024/10/29

* [v0.6.5] Modified the initialization method of the class 'tfmodel' for DAO objects, adding three methods: 'setDAOOneToOne', 'setDAOOneToMan', 'setDAOManyToMan' to initialize composite DAO objects. Independent DAO objects do not require explicit initialization.
* [v0.6.5] Modified the method 'onLoad' of the class 'tfrestfulAPI' to support custom methods such as 'on[REQUEST_METHOD]','on[RESOURCE_FUNCTION]','on[REQUEST_METHOD]_[RESOURCE_FUNCTION]'.
* [v0.6.5] Added the method 'centerCrop' of class 'tfimage' to crop images of different sizes.

## [v0.6.6] - 2024/12/08

* [v0.6.6] Added the model 'tffastCRUDModel' and controller 'tffastCRUDController' to quickly build backend functions for adding, retrieving, modifying, and deleting.
* [v0.6.6] Custom functions 'set_error_handler' and 'set_exception_handler' have been set up to display more detailed error and exception information.
* [v0.6.6] Added class' tfget ',' tfpost'、'tffiles'、'tfcookie'、'tfserver'、'tfsession'、'tfglobals'， Used to obtain input data.
* [v0.6.6] Added the 'realRemoteAddr' method to the 'tfserver' class to obtain the real client IP address.
* [v0.6.6] Added the 'rawData' method to the 'tfrequest' class to retrieve raw client submitted data.

## [v0.6.7] - 2025/05/21

* [v0.6.7] The data source has added a parameter 'table_prefix' to set the prefix string for the data table. This data table prefix is only used for database operations, and the 'DAO' object name corresponding to the data table is not included.
* [v0.6.7] Added 'JSON', 'HTML', and 'PlainText' methods to the 'tfresponse' class to directly output 'JSON' data, 'HTML' data, and 'PlainText' plain text data.
* [v0.6.7] Modify some method names of the 'tfresponse' class, changing method 'responsivJsonData' to 'responsiveJSONData', method 'responsiveHTMLDData' to 'responsiveHTMData', and method 'responsivePanetext' to 'responsivePaneText'.
* [v0.6.7] Modify some methods of the 'tfresponse' class that contain the $stopScript parameter, removing the $stopScript parameter. When using it, you can terminate the code by returning tfresponse:: xxx.
* [v0.6.7] Added the 'setCookie' method to the 'tfcookie' class, supporting complete functionality for setting 'Cookie' data.
* [v0.6.7] Modify the transaction processing methods of class' tfdo 'and add support for' SAVEPOINT 'to achieve support for multi-layer transaction processing.
* [v0.6.7] Organized three classes, 'tfdao', 'tfdao Single', and 'tfdao OneToOne', and defined empty methods for the common methods of the 'tfdao Single' and 'tfdao OneToOne' classes in the common base class' tfdao '.
* [v0.6.7] Added error codes to all classes in the framework that throw exceptions.
* [v0.6.7] Organized the 'tfapi' class and simplified the response function.
* [v0.6.7] Organized the 'tfrestfulAPI' class and simplified the response function.
* [v0.6.7] Added the 'onLoadCustom' method to the 'tfrestfulAPI' class, which switches to the 'onLoadCustom' custom method when no available response method is found.
* [v0.6.7] Adjusted two methods of 'tfdaoManyToMan', changing 'insert' to 'insertOne' and 'delete' to 'deleteOne'.
* [v0.6.7] Added class' tfcrudBuilder 'to enable rapid development of interfaces for' CRUD 'operations.
* [v0.6.7] Added 'getResourceValue' and 'getResourceFunction' methods to the 'tfresponse' class to retrieve access resource values and access resource functionality.
* [v0.6.7] Removed the model 'tffastCRUDModel' and controller 'tffastCRUDController'.
* [v0.6.7] Added support for built-in variables such as' $tfphp ',' $get ',' $post ',' $files', '$cookie', '$server', and '$session' to the 'tfview' class, greatly simplifying the code volume for template calls to system data.
* [v0.6.7] Added the 'buildBreadCrumb' method to the 'tfcrudBuilder' class to retrieve breadcrumb data.
* [v0.6.7] Added 'options' parameter to the' select 'related methods of the' tfdaoSngle 'class, and set the' selectFields' sub parameter in the 'options' parameter to set the field information for querying the database.
* [v0.6.7] Added the 'options' parameter to the' select 'related methods of the' tfdaoOneToOne 'class, and set the' selectFields' sub parameter in the 'options' parameter to set the field information for querying the database.
* [v0.6.7] Added the 'setFields' method to the' tfdaoSngle 'class, and implemented parameter friendly input for data query methods through the' methodChaining 'method.
* [v0.6.7] Added the 'setFields' method to the' tfdaoOneToOne 'class, and implemented parameter friendly input for data query methods through the' methodChaining 'method.
* [v0.6.7] All methods of the 'tfdaoOneToMan' class have been removed because it is more convenient to directly manipulate one to many data structures during binding, unbinding, and querying.
* [v0.6.7] Removed all methods of the 'tfdaoManyToMan' class and added several new methods.
* [v0.6.7] Added a 'bind' method to the 'tfdaoManyToMan' class to associate data from Table A and Table B through Table M.
* [v0.6.7] Added the 'unbind' method to the 'tfdaoManyToMan' class to disassociate data from table A and table B.
* [v0.6.7] Added a 'replace' method to the 'tfdaoManyToMan' class to replace the existing association between table A data and table B data.
* [v0.6.7] Added the 'getADataAll' method to the 'tfdaoManyToMan' class to retrieve all data associated with table B in table A.
* [v0.6.7] Added the 'getBDataAll' method to the 'tfdaoManyToMan' class to retrieve all B table data associated with A table data.
* [v0.6.7] Changed the null return of 'many' and 'all' methods in 'tfdo' to an empty array.
* [v0.6.7] Changed the return of null for 'tfdao', 'tfdao Single', 'tfdaoOneToOne', 'tfdaoOneToMan', 'tfdaoAnyToMan' methods related to 'Man' and 'All' methods to an empty array.
* [v0.6.7] Added the 'setOrders' method to the' tfdaoSngle 'class to set the sorting of query data.
* [v0.6.7] Added the 'setOrders' method to the' tfdaoOneToOne 'class to set the sorting of query data.