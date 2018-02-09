# redis

Redis is an open source (BSD licensed), in-memory data structure store, used as a database, cache and message broker. 

## docker image

[docker image 说明](https://hub.docker.com/_/redis/)，该项目使用的是redis/latest.

## 配置文件

配置文件是`conf/redis.conf`

重要配置项
```
绑定的地址
bind 127.0.0.1

监听的端口
port 6379

redis密码
requirepass 27594e8a-877e-11e5-bd52-6bced77c06ee
```

## 操作说明

```
进入redis的container
sudo docker exec -it qtalk-redis /bin/bash

停止该服务
sudo docker stop qtalk-redis

启动该服务
sudo docker start qtalk-redis

重启该服务
sudo docker restart qtalk-redis

删除该container
sudo docker rm -f qtalk-redis
```

## redis 简单操作命令
```
[monkboy@localhost ~]$ sudo docker exec -it qtalk-redis /bin/bash
root@localhost:/data# redis-cli                                                                                                                                                               
127.0.0.1:6379> auth 27594e8a-877e-11e5-bd52-6bced77c06ee
OK
127.0.0.1:6379> SELECT 1
OK
127.0.0.1:6379[1]> set "key" "value"
OK
127.0.0.1:6379[1]> get "key"
"value"
127.0.0.1:6379[1]> exit
[root@localhost /]# exit
[monkboy@localhost ~]$ 
```

## redis 操作文档

[redis 官网](https://redis.io/)
[redis 命令](http://redisdoc.com/)

## 备注