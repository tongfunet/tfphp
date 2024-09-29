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
content longtext not null,
createDT datetime not null,
updateDT datetime null,
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