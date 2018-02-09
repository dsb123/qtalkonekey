# qtalk_http_service

该服务是通过java提供的文件服务的接口，数据存储使用的是swift

## docker image

[docker image 说明](https://hub.docker.com/_/tomcat/)，该项目使用的是tomcat:8.0.20-jre8.

## 操作说明

```
进入container
sudo docker exec -it qtalk_http_service /bin/bash

停止该服务
sudo docker stop qtalk_http_service

启动该服务
sudo docker start qtalk_http_service

重启该服务
sudo docker restart qtalk_http_service

删除该container
sudo docker rm -f qtalk_http_service
```

## 提供的端口

8888

## qtalk_http_service 配置文件
|文件 |配置项 | 含义 | 备注 |
|:--|--:|--:|--:|
|conf/app.properties| qtalk.empd.cookie | erlang的cookie | |
|conf/app.properties| qtalk.peer.node | ejabberd服务的node | |
|conf/app.properties| qtalk.empd.node | 本节点node | |
|conf/app.properties| erlang.empd.mbox | 信箱 | |
|conf/app.properties| http_node | qtalk_cowboy_server的节点 | |
|conf/app.properties| url.user.vcard | | |
|conf/app.properties| url.user.vcard.bnb | | |
|conf/jdbc.properties| message.database.driver | 数据库驱动 | |
|conf/jdbc.properties| message.database.url | 数据库地址 | |
|conf/jdbc.properties| message.database.username | 数据库用户名 | |
|conf/jdbc.properties| message.database.password | 数据库密码 | |
|conf/jdbc.properties| qchat.database.url | 和上面保持一致 | |
|conf/jdbc.properties| qchat.database.username | 和上面保持一致 | |
|conf/jdbc.properties| qchat.database.password | 和上面保持一致 | |
|conf/redis.properties| * | * | 连接的redis相关配置 |
|conf/spring_servlet.xml| * | * | spring相关配置 |
|server.xml| * | * | tomcat配置 |

安装完成后，conf下的文件会被复制到./qtalk_http_service/WEB-INF/classes/下面，修改这下面的文件，然后重启该container就生效了

## 备注