# swift openresty

swift的前置

## docker image

docker-qunar.repo.corp.qunar.com/openresty/openresty

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

## openresty 配置文件
openresty的配置都在conf/下面

## 提供的端口

8088

## openresty 操作文档

[agentzh 的 Nginx 教程](https://openresty.org/download/agentzh-nginx-tutorials-zhcn.html)
[openresty 官网](https://openresty.org/cn/)

## 备注