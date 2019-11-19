FROM registry.cn-beijing.aliyuncs.com/nfangxu/laranginx:1.2

WORKDIR /var/www/html

# composer 镜像源
RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# composer install
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# 复制文件
COPY . .

# finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

# 更改目录权限
RUN chown -R www-data /var/www/html/public && chown -R www-data /var/www/html/storage

# 配置文件
RUN cp .env.example .env

EXPOSE 443 80

CMD ["/init.sh"]