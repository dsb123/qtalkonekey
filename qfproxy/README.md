# qfproxy

该服务是通过java提供的文件服务的接口，数据存储使用的是swift

## docker image

[docker image 说明](https://hub.docker.com/_/tomcat/)，该项目使用的是tomcat:8.0.20-jre8.

## 操作说明

```
进入container
sudo docker exec -it qfproxy /bin/bash

停止该服务
sudo docker stop qfproxy

启动该服务
sudo docker start qfproxy

重启该服务
sudo docker restart qfproxy

删除该container
sudo docker rm -f qfproxy
```

## 提供的端口

8000

## qfproxy 配置文件
|文件 |配置项 | 含义 | 备注 |
|:--|--:|--:|--:|
|conf/outer_va_redis.properties| * | * | 连接redis的配置 |
|conf/qfproxy.properties| * | * | 返回的文件地址的前缀地址 |
|conf/storage.properties| * | * | 连接swift的配置 |
|server.xml| * | * | tomcat配置 |


安装完成后，conf下的文件会被复制到./qfproxy/WEB-INF/classes/下面，修改这下面的文件，然后重启该container就生效了


## 备注