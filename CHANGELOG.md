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

## [v0.5.2] - 2024/8/1

* [0.5.2] Added the built-in tfphp tool build_mase_madels_by_datasource to build the basic model of the tfphp framework through MySQL database connection.
