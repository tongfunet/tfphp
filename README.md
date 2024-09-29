<h1 align="center">TFPHP v0.6.0</h1>

## TFPHP

TFPHP is a web application framework based on the PHP language. We believe that an excellent framework should first be able to achieve rapid development of web applications, secondly, the syntax should be very elegant, and finally, the code readability should be very high.

## Learning TFPHP

The TFPHP framework is still undergoing continuous improvement. Interested friends, please continue to follow us.

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

In this example, all requests will be redirected to the FPM server. If you want to separate static and dynamic content, you can set it like this:

```apacheconf
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root           /var/www/html;
    index          index.html index.htm index.php;

    if ( $uri !~* (\.(zip|rar|gz|bz|7z|jpg|jpeg|png|gif))$ ) {
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

This is an example of a regular webpage, where the controller object extends the class tfphp\framework\framework\framework\system\tfpage:

```php
namespace tfphp\framework\framework\controller;

use tfphp\framework\framework\framework\system\tfpage;

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

This is an example of an API interface, where the controller object extension class tfphp\framework\framework\framework\system\tfapi:

```php
namespace tfphp\framework\framework\controller\api;

use tfphp\framework\framework\framework\system\tfapi;

class userState extends tfapi {
    protected function onLoad(){
        $this->responsePlaintextData("this is a demo for API /userState");
    }
}
```

This is an example of a RESTful style interface, where the controller object extends class tfphp\framework\framework\framework\system\tfrestfulAPI:

```php
namespace tfphp\framework\framework\controller\api;

use tfphp\framework\framework\framework\system\tfrestfulAPI;

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

This is the SQL statement for the data table required by the testing program:

```sql
create table user(
userId int not null auto_increment,
userName varchar(45) not null,
userPwd char(32) not null,
state tinyint not null default 0,
createDT datetime not null,
updateDT datetime null,
primary key(userId),
unique u_userName(userName)
);

create table userDetail(
userId int not null,
nickName varchar(45) null,
gender tinyint null,
birth date null,
description varchar(254),
primary key(userId)
);

create table article(
articleId int not null auto_increment,
classId int not null default 0,
title varchar(100) not null,
primary key(articleId)
);

create table articleClass(
classId int not null auto_increment,
className varchar(45) not null,
primary key(classId),
unique u_className(className)
);

create table articleTag(
tagId int not null auto_increment,
tagName varchar(45) not null,
primary key(tagId),
unique u_tagName(tagName)
);

create table article_tag(
articleId int not null,
tagId int not null,
primary key(articleId, tagId)
);
```

This is the configuration file for the testing program:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<TFPHP xmlns="http://tongfu.net/tfphp/1.0.0">
    <database>
        <default driver="mysql" host="tfmysql" port="3306" username="root" password="abcdef" database="test"></default>
    </database>
</TFPHP>
```

This is the model class for the user module of the testing program:

```php
namespace tfphp\framework\framework\model;

use tfphp\framework\framework\framework\tfphp;
use tfphp\framework\framework\framework\model\tfmodel;
use tfphp\framework\framework\framework\model\tfdao;
use tfphp\framework\framework\framework\model\tfdaoSingle;
use tfphp\framework\framework\framework\model\tfdaoOneToOne;

class user extends tfmodel{
    public function __construct(tfphp $tfphp){
        $tableUser = new tfdaoSingle($tfphp, [
            "name"=>"user",
            "fields"=>[
                "userId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "userName"=>["type"=>tfdao::FIELD_TYPE_STR],
                "userPwd"=>["type"=>tfdao::FIELD_TYPE_STR],
                "state"=>["type"=>tfdao::FIELD_TYPE_INT],
                "createDT"=>["type"=>tfdao::FIELD_TYPE_STR],
                "updateDT"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["userId"],
                "userName"=>["userName"]
            ],
            "autoIncrementField"=>"userId"
        ]);
        $tableUserDetail = new tfdaoSingle($tfphp, [
            "name"=>"userDetail",
            "fields"=>[
                "userId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "nickName"=>["type"=>tfdao::FIELD_TYPE_STR],
                "gender"=>["type"=>tfdao::FIELD_TYPE_INT],
                "birth"=>["type"=>tfdao::FIELD_TYPE_STR],
                "description"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["userId"]
            ]
        ]);
        parent::__construct($tfphp, [
            "user"=>new tfdaoOneToOne($tfphp, [
                $tableUser,
                $tableUserDetail
            ], [
                "fieldMapping"=>[
                    [
                        "userId"=>"userId"
                    ]
                ]
            ])
        ]);
    }
}
```

This is the controller class for the user module of the testing program:

```php
namespace tfphp\framework\framework\controller\api\test;

use tfphp\framework\framework\framework\system\tfapi;
use tfphp\framework\framework\model\user as modelUser;

class user extends tfapi {
    protected function onLoad(){
        $user = new modelUser($this->tfphp);
        try {
            $this->tfphp->responsePlaintextData("");
            $daoUser = $user->getDAOOneToOne("user");

            $ret = $daoUser->insert([
                "userName"=>"鬼谷子叔叔",
                "userPwd"=>md5("123456"),
                "createDT"=>date("Y-m-d H:i:s"),
                "nickName"=>"鬼谷子叔叔",
                "description"=>"这是鬼谷子叔叔的个人介绍"
            ]);
            var_dump("insert", $ret);

            $userInfo = $daoUser->getLastInsert();
            var_dump("getLastInsert", $userInfo);

            $userInfo = $daoUser->select([
                $daoUser->getAutoIncrementField()=>$daoUser->getLastInsertAutoIncrementValue()
            ]);
            var_dump("select by auto-increment field", $userInfo);

            $userInfo = $daoUser->select([
                "userName"=>"鬼谷子叔叔"
            ], "userName");
            var_dump("select by userName", $userInfo);

            $ret = $daoUser->update($userInfo, [
                "updateDT"=>date("Y-m-d H:i:s"),
                "nickName" => "鬼谷子叔叔！",
                "description"=>"这是鬼谷子叔叔的个人介绍！"
            ], "default");
            var_dump("update", $ret);

            $userInfo = $daoUser->select($userInfo, "default");
            var_dump("select after update", $userInfo);

            $ret = $daoUser->delete($userInfo, "default");
            var_dump("delete", $ret);
        }
        catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}
```

This is the model class of the article module of the testing program:

```php
namespace tfphp\framework\framework\model;

use tfphp\framework\framework\framework\tfphp;
use tfphp\framework\framework\framework\model\tfmodel;
use tfphp\framework\framework\framework\model\tfdao;
use tfphp\framework\framework\framework\model\tfdaoSingle;
use tfphp\framework\framework\framework\model\tfdaoOneToMany;
use tfphp\framework\framework\framework\model\tfdaoManyToMany;

class article extends tfmodel{
    public function __construct(tfphp $tfphp){
        $tableArticle = new tfdaoSingle($tfphp, [
            "name"=>"article",
            "fields"=>[
                "articleId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "classId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "title"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["articleId"],
                "classId"=>["classId"]
            ],
            "autoIncrementField"=>"articleId"
        ]);
        $tableArticleClass = new tfdaoSingle($tfphp, [
            "name"=>"articleClass",
            "fields"=>[
                "classId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "className"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["classId"],
                "className"=>["className"]
            ],
            "autoIncrementField"=>"classId"
        ]);
        $tableArticleTag = new tfdaoSingle($tfphp, [
            "name"=>"articleTag",
            "fields"=>[
                "tagId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "tagName"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["tagId"],
                "tagName"=>["tagName"]
            ],
            "autoIncrementField"=>"tagId"
        ]);
        $tableArticle_tag = new tfdaoSingle($tfphp, [
            "name"=>"article_tag",
            "fields"=>[
                "articleId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "tagId"=>["type"=>tfdao::FIELD_TYPE_INT],
            ],
            "constraints"=>[
                "default"=>["articleId", "tagId"]
            ]
        ]);
        parent::__construct($tfphp, [
            "article"=>$tableArticle,
            "articleClass"=>$tableArticleClass,
            "articleTag"=>$tableArticleTag,
            "article_properties"=>new tfdaoOneToMany($tfphp, [
                $tableArticle,
                $tableArticleClass
            ], [
                "fieldMapping"=>[
                    [
                        "classId"=>"classId"
                    ]
                ]
            ]),
            "article_tags"=>new tfdaoManyToMany($tfphp, [
                $tableArticle,
                $tableArticleTag,
                $tableArticle_tag
            ], [
                "fieldMapping"=>[
                    [
                        "articleId"=>"articleId"
                    ], [
                        "tagId"=>"tagId"
                    ]
                ]
            ])
        ]);
    }
}
```

This is the controller class of the test program's article module:

```php
namespace tfphp\framework\framework\controller\api\test;

use tfphp\framework\framework\framework\system\tfapi;
use tfphp\framework\framework\model\article as modelArticle;

class article extends tfapi {
    protected function onLoad(){
        $article = new modelArticle($this->tfphp);
        try {
            $this->tfphp->responsePlaintextData("");
            $daoArticle = $article->getDAOSingle("article");
            $daoArticleClass = $article->getDAOSingle("articleClass");
            $daoArticleTag = $article->getDAOSingle("articleTag");
            $daoArticle_properties = $article->getDAOOneToMany("article_properties");
            $daoArticle_tags = $article->getDAOManyToMany("article_tags");
            $articleInfo = null;
            $articleClassInfo = null;
            $articleTagInfos = [];
            $tags = [
                "PHP", "Python", "Golang"
            ];

            $ret = $daoArticle->insert([
                "title"=>"测试". date("Y-m-d H:i:s")
            ]);
            var_dump("insert article", $ret);

            $articleInfo = $daoArticle->getLastInsert();
            var_dump("getLastInsert article", $articleInfo);

            $ret = $daoArticleClass->insert([
                "className"=>"Technology"
            ]);
            var_dump("insert article class", $ret);

            $articleClassInfo = $daoArticleClass->getLastInsert();
            var_dump("getLastInsert article class", $articleClassInfo);

            $ret = $daoArticle_properties->update([
                $articleInfo,
                $articleClassInfo
            ]);
            var_dump("update article properties", $ret);

            $articleInfo = $daoArticle->select($articleInfo, "default");
            var_dump("select after update", $articleInfo);

            foreach ($tags as $tag){
                $ret = $daoArticleTag->insert([
                    "tagName"=>$tag
                ]);
                var_dump("insert article tag", $ret);
                $articleTagInfo = $daoArticleTag->getLastInsert();
                var_dump("getLastInsert article tag", $articleTagInfo);
                $articleTagInfos[] = $articleTagInfo;
            }

            $ret = $daoArticle_tags->insertMultiple([
                $articleInfo,
                $articleTagInfos
            ]);
            var_dump("insert article tags", $ret);

            $results = $this->tfphp->getDataSource()->fetchAll("SELECT * 
                FROM article a 
                LEFT JOIN articleClass ac ON a.classId = ac.classId", []);
            var_dump("article infos", $results);

            $results = $this->tfphp->getDataSource()->fetchAll("SELECT * 
                FROM article a 
                INNER JOIN article_tag a_t ON a.articleId = a_t.articleId 
                INNER JOIN articleTag at ON a_t.tagId = at.tagId", []);
            var_dump("article tags", $results);

            $daoArticle_tags->deleteMultiple([
                $articleInfo,
                $articleTagInfos
            ]);
            var_dump("delete article tags", $ret);

            foreach ($articleTagInfos as $articleTagInfo){
                $ret = $daoArticleTag->delete($articleTagInfo, "default");
                var_dump("delete article tag", $ret);
            }

            $ret = $daoArticleClass->delete($articleClassInfo, "default");
            var_dump("delete article class", $ret);

            $ret = $daoArticle->delete($articleInfo, "default");
            var_dump("delete article", $ret);
        }
        catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}
```

## License

The TFPHP framework is open-sourced software licensed under the <a href="https://opensource.org/licenses/MIT">MIT license</a>.