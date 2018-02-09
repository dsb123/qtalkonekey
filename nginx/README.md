# openresty

OpenResty® 是一个基于 Nginx 与 Lua 的高性能 Web 平台，其内部集成了大量精良的 Lua 库、第三方模块以及大多数的依赖项。用于方便地搭建能够处理超高并发、扩展性极高的动态 Web 应用、Web 服务和动态网关。

## docker image

[docker image 说明](https://hub.docker.com/r/openresty/openresty/)，该项目使用的是openresty/openresty:trusty.

## 操作说明

```
进入container
sudo docker exec -it openresty /bin/bash

停止该服务
sudo docker stop openresty

启动该服务
sudo docker start openresty

重启该服务
sudo docker restart openresty

删除该container
sudo docker rm -f openresty
```

## 提供的端口

80

## openresty 配置文件
openresty的配置都在conf/下面


## openresty 操作文档

[agentzh 的 Nginx 教程](https://openresty.org/download/agentzh-nginx-tutorials-zhcn.html)
[openresty 官网](https://openresty.org/cn/)

## 备注