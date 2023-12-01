FROM nginx:stable-alpine

RUN mkdir -p /var/www/html/public

ADD ./default.conf /etc/nginx/conf.d/default.conf
