#!/bin/bash

set -e

sudo systemctl start firewalld.service || true
sudo systemctl enable firewalld.service || true

# 系统使用的端口
sudo firewall-cmd --zone=public --add-port=22/tcp --permanent
sudo firewall-cmd --zone=public --add-port=53/tcp --permanent
sudo firewall-cmd --zone=public --add-port=111/tcp --permanent

# 服务使用的端口
sudo firewall-cmd --zone=public --add-port=80/tcp --permanent
sudo firewall-cmd --zone=public --add-port=5222/tcp --permanent
sudo firewall-cmd --zone=public --add-port=5223/tcp --permanent
sudo firewall-cmd --zone=public --add-port=5201/tcp --permanent
sudo firewall-cmd --zone=public --add-port=5202/tcp --permanent
sudo firewall-cmd --zone=public --add-port=9990/tcp --permanent
sudo firewall-cmd --reload

workdir=`dirname $(readlink -f $0)`

ejabberd_hosts_host="l-ejabberd.hosts.cn"
qtalk_cowboy_server_host="l-qtalk.cowboy.server.cn"
nginx_host="l-nginx.cn"
php_host="l-php.cn"
qtalk_admin_host="l-qtalk.admin.cn"
qtalk_http_service_host="l-qtalk.http.service.cn"
qtalk2es_host="l-qtalk2es.cn"
qfproxy_host="l-qfproxy.cn"
swift_nginx_host="l-swift.nginx.cn"
swift_host="l-swift.cn"
redis_host="l-redis.cn"
elasticsearch_host="l-elasticsearch.cn"
postgres_host="l-postgres.cn"
redis_password="`uuidgen`"
postgres_password="`uuidgen`"
search_host="l-search.cn"

while [ $# -ne 0 ] ; do
    PARAM="$1"
    shift
    case $PARAM in
    	--hosturl) HOSTURL=$1 ; shift ;;
    	--hostname) HOST=$1 ; shift ;;
        *) ;;
    esac
done

echo -e "the hosturl is: \033[0;31m${HOSTURL}\033[0m"
echo -e "the hostname is: \033[0;31m${HOST}\033[0m"
echo -e -n "Continue?\033[0;31m[YES/NO]: \033[0m"
read input

if [ $input = "YES" ]; then
	sed -i "s%<?QTALK base_dir>%${workdir}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk elasticsearch>%${elasticsearch_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk swift>%${swift_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk swift-nginx>%${swift_nginx_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk qfproxy>%${qfproxy_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk qtalk_http_service>%${qtalk_http_service_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk qtalk2es>%${qtalk2es_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk php>%${php_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk search>%${search_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk qtalk_admin>%${qtalk_admin_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk nginx>%${nginx_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk qtalk_cowboy_server>%${qtalk_cowboy_server_host}%g" ${workdir}/docker-compose.yml
	sed -i "s%<?qtalk ejabberd-hosts>%${ejabberd_hosts_host}%g" ${workdir}/docker-compose.yml


	# php
	sed -i "s%<?qtalk HOSTURL>%${HOSTURL}%g" ${workdir}/php/qtalk_php/nav.php
	sed -i "s%<?qtalk HOSTURL>%${HOSTURL}%g" ${workdir}/php/qtalk_php/subnav.php

	# search
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/search/php/common/db_conf.sentinel.php
	sed -i "s%<?qtalk redis_password>%${redis_password}%g" ${workdir}/search/php/common/db_conf.sentinel.php
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/search/php/common/define.php
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g" ${workdir}/search/php/common/define.php

	sed -i "s%<?qtalk elasticsearch>%${elasticsearch_host}%g" ${workdir}/search/php/html/lookback/model/ServerAPIModel.php
	sed -i "s%<?qtalk qtalk_cowboy_server>%${qtalk_cowboy_server_host}%g" ${workdir}/search/php/html/lookback/model/ServerAPIModel.php

	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/search/php/html/lookback/model/db_conf.sentinel.php
	sed -i "s%<?qtalk redis_password>%${redis_password}%g" ${workdir}/search/php/html/lookback/model/db_conf.sentinel.php
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/search/php/html/lookback/model/pg_conf.php
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g" ${workdir}/search/php/html/lookback/model/pg_conf.php

	# swift nginx
	sed -i "s%<?qtalk swift>%${swift_host}%g" ${workdir}/swift/nginx/conf.d/vh_swift.conf

	# redis
	sed -i "s%<?qtalk redis_password>%${redis_password}%g" ${workdir}/redis/conf/redis.conf

	# qtalk_admin
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/qtalk_admin/php/common/config.php
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g"  ${workdir}/qtalk_admin/php/common/config.php
	sed -i "s%<?qtalk qtalk_http_service>%${qtalk_http_service_host}%g" ${workdir}/qtalk_admin/php/mainSite/public_method_ajax.php

	# qtalk_http_service
	sed -i "s%<?qtalk ejabberd-hosts>%${ejabberd_hosts_host}%g" ${workdir}/qtalk_http_service/conf/app.properties
	sed -i "s%<?qtalk qtalk_cowboy_server>%${qtalk_cowboy_server_host}%g" ${workdir}/qtalk_http_service/conf/app.properties
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/qtalk_http_service/conf/jdbc.properties
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g" ${workdir}/qtalk_http_service/conf/jdbc.properties
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/qtalk_http_service/conf/redis.properties
	sed -i "s%<?qtalk redis_password>%${redis_password}%g" ${workdir}/qtalk_http_service/conf/redis.properties

	# qtalk2es
	sed -i "s%<?qtalk elasticsearch>%${elasticsearch_host}%g" ${workdir}/qtalk2es/conf/app.properties
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/qtalk2es/conf/jdbc.properties
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g" ${workdir}/qtalk2es/conf/jdbc.properties

	# ejabberd-hosts
	sed -i "s%<? HOSTNAME>%${HOST}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberd.yml
	sed -i "s%<?qtalk HOSTURL>%${HOSTURL}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberd.yml
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberd.yml
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberd.yml
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberd.yml
	sed -i "s%<?qtalk redis_password>%${redis_password}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberd.yml
	sed -i "s%<?qtalk ejabberd-hosts>%${ejabberd_hosts_host}%g" ${workdir}/ejabberd-hosts/etc/ejabberd/ejabberdctl.cfg


	# nginx
	sed -i "s%<?qtalk php>%${php_host}%g" ${workdir}/nginx/conf/conf.d/upstreams/qt.qunar.com.upstream.conf
	sed -i "s%<?qtalk search>%${search_host}%g" ${workdir}/nginx/conf/conf.d/upstreams/qt.qunar.com.upstream.conf
	sed -i "s%<?qtalk qfproxy>%${qfproxy_host}%g" ${workdir}/nginx/conf/conf.d/upstreams/qt.qunar.com.upstream.conf
	sed -i "s%<?qtalk qtalk_http_service>%${qtalk_http_service_host}%g" ${workdir}/nginx/conf/conf.d/upstreams/qt.qunar.com.upstream.conf
	sed -i "s%<?qtalk qtalk_cowboy_server>%${qtalk_cowboy_server_host}%g" ${workdir}/nginx/conf/conf.d/upstreams/qt.qunar.com.upstream.conf
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/nginx/lua_app/checks/qim/qtalkredis.lua

	# qfproxy
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/qfproxy/conf/outer_va_redis.properties
	sed -i "s%<?qtalk HOSTURL>%${HOSTURL}%g" ${workdir}/qfproxy/conf/qfproxy.properties
	sed -i "s%<?qtalk swift-nginx>%${swift_nginx_host}%g" ${workdir}/qfproxy/conf/storage.properties
	sed -i "s%<?qtalk swift>%${swift_host}%g" ${workdir}/qfproxy/conf/storage.properties

	# qtalk_cowboy_server
	sed -i "s%<?qtalk redis>%${redis_host}%g" ${workdir}/qtalk_cowboy_server/config/ejb_http_server.config
	sed -i "s%<?qtalk postgres>%${postgres_host}%g" ${workdir}/qtalk_cowboy_server/config/ejb_http_server.config
	sed -i "s%<?qtalk ejabberd-hosts>%${ejabberd_hosts_host}%g" ${workdir}/qtalk_cowboy_server/config/ejb_http_server.config
	sed -i "s%<?qtalk postgres_password>%${postgres_password}%g" ${workdir}/qtalk_cowboy_server/config/ejb_http_server.config
	sed -i "s%<?qtalk redis_password>%${redis_password}%g" ${workdir}/qtalk_cowboy_server/config/ejb_http_server.config
	sed -i "s%<? HOST>%${HOST}%g" ${workdir}/qtalk_cowboy_server/config/ejb_http_server.config
	sed -i "s%<?qtalk qtalk_cowboy_server>%${qtalk_cowboy_server_host}%g" ${workdir}/qtalk_cowboy_server/bin/qtalk_cowboy_server

	if [ -d ${workdir}/qtalk_cowboy_server/qtalk_cowboy_server ]; then
    	sudo rm -rf ${workdir}/qtalk_cowboy_server/qtalk_cowboy_server
	fi
	unzip qtalk_cowboy_server/qtalk_cowboy_server.zip -d qtalk_cowboy_server/

	if [ -d ${workdir}/ejabberd-hosts/ejabberd-hosts ]; then
    	sudo rm -rf ${workdir}/ejabberd-hosts/ejabberd-hosts
	fi
	unzip ejabberd-hosts/ejabberd-hosts.zip -d ejabberd-hosts/

    if [ -d ${workdir}/qfproxy/qfproxy ]; then
        sudo rm -rf ${workdir}/qfproxy/qfproxy
    fi
    unzip qfproxy/qfproxy.war -d qfproxy/qfproxy
    cp ${workdir}/qfproxy/conf/* ${workdir}/qfproxy/qfproxy/WEB-INF/classes/

    if [ -d ${workdir}/qtalk_http_service/qtalk_http_service ]; then
        sudo rm -rf ${workdir}/qtalk_http_service/qtalk_http_service
    fi
    unzip qtalk_http_service/qtalk_http_service.war -d qtalk_http_service/qtalk_http_service
    cp ${workdir}/qtalk_http_service/conf/* ${workdir}/qtalk_http_service/qtalk_http_service/WEB-INF/classes/

    if [ -d ${workdir}/qtalk2es/qtalk2es ]; then
        sudo rm -rf ${workdir}/qtalk2es/qtalk2es
    fi
    unzip qtalk2es/qtalk2es.war -d qtalk2es/qtalk2es
    cp ${workdir}/qtalk2es/conf/* ${workdir}/qtalk2es/qtalk2es/WEB-INF/classes/

	sudo chmod +x ${workdir}/qtalk_cowboy_server/bin/qtalk_cowboy_server
	sudo chmod +x ${workdir}/qtalk_cowboy_server/bin/start.sh
	sudo chmod +x ${workdir}/ejabberd-hosts/sbin/ejabberdctl
	sudo chmod +x ${workdir}/ejabberd-hosts/sbin/start.sh

	sudo chmod 400 ${workdir}/qtalk_cowboy_server/.erlang.cookie
	sudo chmod 400 ${workdir}/ejabberd-hosts/.erlang.cookie

	echo "安装docker"
	sudo yum -y install docker
	# sudo journalctl -u docker -f

	echo "启动和设置docker"
	sudo systemctl start docker.service
	sudo systemctl enable docker.service
	sudo systemctl status docker.service

	# 加载docker镜像
    if [ -d ${workdir}/images ]; then
		sudo docker load < images/busybox_1.0.0.tar || true
		sudo docker load < images/tomcat_8.0.20-jre8.tar || true
		sudo docker load < images/php_5.6-apache-pgsql-redis.tar || true
		sudo docker load < images/postgres_10.1.tar || true
		sudo docker load < images/redis_1.0.0.tar || true
		sudo docker load < images/docker-swift-onlyone.tar || true
		sudo docker load < images/elasticsearch_2.4.tar || true
		sudo docker load < images/openresty_trusty.tar || true
		sudo docker load < images/erlang_17.5.tar || true
		sudo docker load < images/erlang_19.3.tar || true
	else
		cd ${workdir}/dockerfile/erlang1705
		sudo docker build -t erlang:17.5 .

		cd ${workdir}/dockerfile/erlang1903
		sudo docker build -t erlang:19.3 .

		cd ${workdir}/dockerfile/php5.6-pgsql
		sudo docker build -t php:5.6-apache-pgsql-redis .
	fi

	cd ${workdir}
	# 删除所有container
	sudo docker rm -f $(sudo docker ps -a -q) || true

	chmod +x ./docker-compose-Linux-x86_64
	sudo ./docker-compose-Linux-x86_64 up -d

	sleep 30
	sudo docker exec -it -u postgres ${postgres_host} psql -c "create database  ejabberd;" || true
	sudo docker exec -it -u postgres ${postgres_host} psql -c "create user ejabberd login superuser password '${postgres_password}';" || true
	sudo docker exec -it -u postgres  ${postgres_host} psql -d ejabberd -f /ejabberd.dump || true
	sudo docker exec -it -u postgres l-postgres.cn psql -d ejabberd -c "insert into host_info (host, description, host_admin) values ('${HOST}', '${HOST}', 'test_admin');" || true
	sudo docker exec -it -u postgres l-postgres.cn psql -d ejabberd -c "insert into host_users (host_id, user_id, user_name, department, dep1, pinyin, frozen_flag, version, user_type, hire_flag, gender, password, initialpwd) values ('1', 'test_admin', 'test_admin', '/度假', '度假', 'lffan.liu', '0', '1', 'U', '1', '0', 'test', '1');" || true
	sudo docker exec -it -u postgres l-postgres.cn psql -d ejabberd -c "insert into host_users (host_id, user_id, user_name, department, dep1, pinyin, frozen_flag, version, user_type, hire_flag, gender, password, initialpwd) values ('1', 'test', 'test', '/度假', '度假', 'lffan.liu', '0', '1', 'U', '1', '0', 'test', '1');" || true

	# 
	sudo docker restart ${ejabberd_hosts_host}
	sleep 30

	sudo docker restart ${qtalk_cowboy_server_host}
	sleep 30

	sudo docker restart ${qtalk_http_service_host}
	sleep 30

	echo "安装结果"
	echo "docker service"
	sudo docker ps

	echo "  ___ _____  _    _     _  __"
	echo " / _ \_   _|/ \  | |   | |/ /"
	echo "| | | || | / _ \ | |   | ' / "
	echo "| |_| || |/ ___ \| |___| . \ "
	echo ' \__\_\|_/_/   \_\_____|_|\_\'

	echo -e "导航地址是：\033[0;31mhttp://${HOSTURL}/php/nav.php\033[0m"
	echo -e "管理界面地址是：\033[0;31mhttp://${HOSTURL}:9990/mainSite/domain_user_manager.php\033[0m"
else
	echo -e "\033[0;31mYou cancel the install.\033[0m"
fi

