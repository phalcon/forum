FROM ubuntu
MAINTAINER "Thien Tran" <fcduythien@gmail.com>
ENV container docker

RUN apt-get update && apt-get install -yqq \
	curl \
    git \
    libxml2-dev \
    python \
    build-essential \
    make \
    gcc \
    python-dev \
    locales \
    python-pip
#

ENV  DEBIAN_FRONTEND noninteractive
ENV  MYSQL_PASSWORD root
ENV  DB_NAME forum

#Adding shell scripts
ADD opsfile/scripts/mysql.sh /tmp/mysql.sh
ADD opsfile/scripts/nginx.sh /tmp/nginx.sh
ADD opsfile/scripts/php.sh /tmp/php.sh
ADD opsfile/scripts/phalcon.sh /tmp/phalcon.sh
ADD opsfile/scripts/app.sh /tmp/app.sh
ADD opsfile/run.sh /tmp/run.sh
ADD schemas/forum.sql /tmp/forum.sql

# Set permision exceute files
RUN chmod 755 /tmp/*.sh

#Install Nginx
RUN ./tmp/nginx.sh

#install php-fpm
RUN ./tmp/php.sh

#install mysql
RUN ./tmp/mysql.sh

#install phalcon
RUN ./tmp/phalcon.sh

# Install php composer and dependency
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable passwordless sudo for the "vagrant" user
#RUN echo 'vagrant ALL=(ALL) NOPASSWD: ALL' > /etc/sudoers.d/vagrant
#CMD /usr/sbin/sshd -D -o UseDNS=no -o UsePAM=no

EXPOSE 80

# Copy site into place.
COPY . /usr/share/nginx/html/www
ADD opsfile/templates/nginx/default.conf /etc/nginx/sites-enabled/default
ADD app/config/config.example.php /usr/share/nginx/html/www/app/config/config.php
RUN ./tmp/app.sh


CMD ["nginx", "-g", "daemon off;"]
CMD ["sh", "/tmp/run.sh"]

#CMD ["php5-fpm" ,"-D"]
#ENTRYPOINT /usr/sbin/php5-fpm -D
#CMD ["/bin/bash", "/tmp/run.sh"]