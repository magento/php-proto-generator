# Overview

This project is a `protoc` plugin for Magento specific gRPC code generation.

# Installation
* This is a `protoc` plugin, so you need to have `protoc` binary installed and visible in PATH
* `composer install`

# Usage
Run `protoc --php_out=tests/tmp/ --php-grpc_out=tests/tmp/ --magento_out=tests/tmp/ --plugin=protoc-gen-grpc=grpc_php_plugin --plugin=protoc-gen-magento=protoc-gen-magento -I tests/fixtures tests/fixtures/basic.proto`


# Testing
Install composer dev dependencies. Run `php vendor/bin/phpunit`.