FROM alpine:3.11
RUN apk --no-cache add \
        php7 \
        php7-ctype \
        php7-curl \
        php7-dom \
        php7-fileinfo \
        php7-ftp \
        php7-iconv \
        php7-json \
        php7-mbstring \
        php7-openssl \
        php7-pear \
        php7-phar \
        php7-posix \
        php7-simplexml \
        php7-tokenizer \
        php7-xml \
        php7-xmlreader \
        php7-xmlwriter \
        php7-zlib \
        protobuf \
        && apk --no-cache add --virtual build-dependencies \
           build-base \
           autoconf \
           automake \
           gcc \
           wget \
           git \
           curl \
           tar \
           libtool \
           make \
           g++ \
           linux-headers \
           libc-dev \
           boost-dev \
           boost-static \
           cmake \
           ninja \
           unzip


RUN EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)" \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")" \
    && if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then \
        echo 'ERROR: Invalid installer checksum' \
        exit 1 \
    ;fi \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php \
    && curl -L https://github.com/spiral/php-grpc/releases/download/v1.1.0/protoc-gen-php-grpc-1.1.0-linux-amd64.tar.gz | tar zx \
    && cp protoc-gen-php-grpc-1.1.0-linux-amd64/protoc-gen-php-grpc /usr/local/bin/ && rm -rf protoc-gen-php-grpc-1.1.0-linux-amd64 \
    && GRPC_DIR=/tmp/grpc/ && git clone -b $(curl -L https://grpc.io/release) https://github.com/grpc/grpc $GRPC_DIR \
    && cd $GRPC_DIR \
    && git submodule update --depth 1 --init \
    && make grpc_php_plugin \
    && cp bins/opt/grpc_php_plugin /usr/local/bin/ \
    && rm -rf $GRPC_DIR \
    && apk del build-dependencies

COPY / /app

WORKDIR /app
RUN composer install

ENTRYPOINT ["/app/generator", "packages:generate"]