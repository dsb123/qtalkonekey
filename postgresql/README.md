# postgresql

PostgreSQL is an object-relational database management system (ORDBMS) based on POSTGRES, Version 4.2, developed at the University of California at Berkeley Computer Science Department. POSTGRES pioneered many concepts that only became available in some commercial database systems much later.

## docker image

该项目使用的是registry.corp.qunar.com/consulting/postgresql:9.5.3.

## 操作说明

```
进入container
sudo docker exec -it postgresql /bin/bash

停止该服务
sudo docker stop postgresql

启动该服务
sudo docker start postgresql

重启该服务
sudo docker restart postgresql

删除该container
sudo docker rm -f postgresql
```

## postgresql 操作文档

[postgresql 官网](https://www.postgresql.org/)

## 备注