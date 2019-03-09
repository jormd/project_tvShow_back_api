FROM ubuntu:16.04

RUN apt-get update \
 && apt-get install -y apt-transport-https ca-certificates \
 && apt-get install -y language-pack-en-base software-properties-common apt-utils

RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en

RUN apt-get install -y software-properties-common \
 && apt-add-repository ppa:ondrej/php
RUN apt-get update
RUN apt-get install -qq -y nginx php7.1-fpm php7.1-pdo php7.1-mysql php7.1-xml

# Nginx config
RUN rm /etc/nginx/sites-enabled/default
ADD ./config/nginxServe.conf /etc/nginx/sites-available/symfony.conf
RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony.conf

# Define default command.
ADD ./ /var/www/html
CMD service nginx start && service php7.1-fpm start && tail -f /var/log/nginx/error.log

# Expose ports.
EXPOSE 80