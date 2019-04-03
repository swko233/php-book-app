FROM php:7.2-apache

# Apacheのweproot変更（CakePHP動作に必要）
ENV APACHE_DOCUMENT_ROOT /var/www/html/app/webroot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
&& sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
&& a2enmod rewrite

# Linuxのパッケージや拡張モジュールをインストール（CakePHP動作に必要）
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zip \
    unzip \
&& docker-php-ext-install -j$(nproc) intl \
&& docker-php-ext-install -j$(nproc) pdo_mysql

# 日付を正しく扱うためにLinuxのタイムゾーンを日本標準時に変更
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# ホストでgit cloneしたものをゲストのコンテナ側にもコピーできるようにCOPY
COPY . /var/www/html

# composerのイメージから直接composerのバイナリファイルをコピー
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:1.7 /usr/bin/composer /usr/bin/composer

# composerをインストール(composerでインストールしたライブラリはgitから除外されてしまうため、composer install実行をイメージに含めておく)
# ただし、開発環境ではホスト側のディレクトリをゲスト側にマウントするので、docker-compose.yml（コンテナ起動時）でもcomposer installした上でマウントする必要あり
RUN cd ./app && \
    composer install --no-dev -o
