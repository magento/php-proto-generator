# Overview

This project is a Magento code generator based on proto files provided

## Installation (Docker)

* Install docker
* `docker build -t magento/proto-generator:latest .` in project root directory 

## Usage

### CLI usage

Run `packages:generate [-c|--composer_version [COMPOSER_VERSION]] [--] <proto-dir> <output-dir>`, where
- `--composer_version|-c` - version of generated composer package, this is an optional argument
- `<proto-dir>` - directory that contains Protobuf files
- `<output-dir>` - result output directory

### Docker usage

Run `docker run --rm -it -v $(pwd):/build magento/proto-generator:latest /build/tests/fixtures/ /build/tests/tmp`
- `-v $(pwd):/build` - mounts directory with proto files inside container in /build path
- `/build/tests/fixtures/` - directory containing proto files
- `/build/tests/tmp` - output directory
- `--composer_version|-c` - version of generated composer package, this is an optional argument

You can mount any amount of directories and point input/output to different directories.


## Testing
Run `docker run --rm -it -v $(pwd):/build --entrypoint="/app/vendor/bin/phpunit" magento/proto-generator:latest `
