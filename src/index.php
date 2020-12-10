<?php

namespace Tqdev\PhpCrudAdmin;

use Tqdev\PhpCrudAdmin\Admin;
use Tqdev\PhpCrudAdmin\Config;
use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;

require '../vendor/autoload.php';

$config = new Config([
    'api' => [
        'username' => 'php-crud-api',
        'password' => 'php-crud-api',
        'database' => 'php-crud-api',
        'controllers' => 'columns',
    ],
    'templatePath' => '../templates',
]);
$request = RequestFactory::fromGlobals();
$ui = new Admin($config);
$response = $ui->handle($request);
ResponseUtils::output($response);
