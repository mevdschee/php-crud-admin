<?php

namespace Tqdev\PhpCrudAdmin;

use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;
use Tqdev\PhpCrudAdmin\Config;
use Tqdev\PhpCrudAdmin\Admin;

require '../vendor/autoload.php';

$config = new Config([
    'api' => [
        'username' => 'sakila',
        'password' => 'sakila',
        'database' => 'sakila',
        'controllers' => 'columns'
    ],
    'templatePath' => '../templates',
]);
$request = RequestFactory::fromGlobals();
$ui = new Admin($config);
$response = $ui->handle($request);
ResponseUtils::output($response);
