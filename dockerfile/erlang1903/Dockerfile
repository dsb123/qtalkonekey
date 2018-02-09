# sudo docker build -t erlang:19.3 .
FROM centos:7

RUN yum -y install make gcc gcc-c++ kernel-devel m4  wget automake openssl-devel unixODBC-devel pkgconfig libSM-devel libxslt ncurses-devel libyaml-devel expat-devel java-1.8.0-openjdk-devel

RUN mkdir /download
WORKDIR /download
RUN wget http://erlang.org/download/otp_src_19.3.tar.gz \
 && tar -zxvf otp_src_19.3.tar.gz \
 && cd otp_src_19.3 \
 && ./configure\
 && make && make install \
 && cd / \
 && rm -rf download

WORKDIR /root
CMD ["/bin/bash"]
