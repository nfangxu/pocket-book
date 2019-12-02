FROM registry-vpc.cn-beijing.aliyuncs.com/nfangxu/docker-for-laravel:1.3

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data /var/www/html/ && cp .env.example .env
RUN composer install --no-scripts --no-dev

EXPOSE 443 80

CMD ["* * * * * /usr/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1"]
