<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Magento\\ProtoGen\\\Test\\', __DIR__);

$tmpDir = dirname(__FILE__) . '/../output/';
define('GENERATED', $tmpDir);

$binDir = __DIR__ . '/../bin/';
define('BIN', $binDir);