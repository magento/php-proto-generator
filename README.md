# Overview

This project is a Magento code generator based on proto files provided

# Installation
* Install docker
* `docker build -t magento/proto-generator:latest .` in project root directory 

# Usage
Run `docker run --rm -it -v $(pwd):/build magento/proto-generator:latest /build/tests/fixtures/ /build/tests/tmp`


# Testing
Run `docker run --rm -it -v $(pwd):/build --entrypoint="/app/vendor/bin/phpunit" magento/proto-generator:latest:latest `