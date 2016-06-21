FROM ubuntu:14.04
MAINTAINER Petr Karmashev <smonkl@bk.ru>

ENV DEBIAN_FRONTEND noninteractive

COPY app/docker/etc/apt /etc/apt

RUN apt-get update
RUN apt-get install -y --force-yes \
    git \
    curl \
    nginx \
    mysql-server-5.6 \
    php7.1-cli \
    php7.1-curl \
    php7.1-fpm \
    php7.1-mysql \
    php7.1-mbstring \
    php7.1-sqlite3 \
    php7.1-intl \
    php7.1-xml

RUN apt-get clean && rm -rf /var/lib/apt/lists/* && rm -rf /tmp/*
RUN curl -sS https://getcomposer.org/installer | php

COPY app/docker/etc /etc

CMD ["bash", "/vagrant/app/docker/init.sh"]

EXPOSE 80