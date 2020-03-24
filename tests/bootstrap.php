<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Magento\\ProtoGen\\\Test\\', __DIR__);

$tmpDir = dirname(__FILE__) . '/../output/';
define('GENERATED', $tmpDir);