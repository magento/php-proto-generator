<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Magento\\ProtoGen\\\Test\\', __DIR__);

$tmpDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
define('GENERATED_TEMP', $tmpDir);