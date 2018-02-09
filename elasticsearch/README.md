# elasticsearch

Elasticsearch is a powerful open source search and analytics engine that makes data easy to explore.

## docker image

[docker image 说明](https://hub.docker.com/_/elasticsearch/)，该项目使用的是2.4.

## 操作说明

```
进入container
sudo docker exec -it elasticsearch /bin/bash

停止该服务
sudo docker stop elasticsearch

启动该服务
sudo docker start elasticsearch

重启该服务
sudo docker restart elasticsearch

删除该container
sudo docker rm -f elasticsearch
```
## 提供的端口

9200

## elasticsearch 配置文件
elasticsearch的配置都在conf/下面

## elasticsearch 操作文档

[elasticsearch 权威指南](https://elasticsearch.cn/book/elasticsearch_definitive_guide_2.x/)
[elasticsearch 官网](https://www.elastic.co/)

## 备注