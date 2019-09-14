<?php

namespace Tqdev\PhpCrudAdmin\Client;

use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config;
use Tqdev\PhpCrudApi\RequestFactory;

class LocalCaller implements ApiCaller
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function call(string $method, string $path, array $args = [], $data = false)
    {
        $config = new Config($this->config);
        $body = '';
        if ($data !== false) {
            $body = json_encode($data);
        }
        $request = RequestFactory::fromString(trim("$method $path\n\n$body"));
        $api = new Api($config);
        $response = $api->handle($request);
        return json_decode($response->getBody(), true);
    }
}
