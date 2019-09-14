<?php

namespace Tqdev\PhpCrudAdmin\Client;

interface ApiCaller
{
    function call(string $method, string $path, array $args = [], $data = false);
}
