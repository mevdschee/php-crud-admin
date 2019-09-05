<?php

namespace Tqdev\PhpCrudAdmin;

use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;
use Tqdev\PhpCrudAdmin\Config;
use Tqdev\PhpCrudAdmin\Admin;

require '../vendor/autoload.php';

$config = new Config([
    'url' => 'http://localhost:8000/api.php',
    'templatePath' => '../templates',
]);
$request = RequestFactory::fromGlobals();
$ui = new Admin($config);
$response = $ui->handle($request);
ResponseUtils::output($response);
