<?php

namespace Tqdev\PhpCrudAdmin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tqdev\PhpCrudApi\Cache\CacheFactory;
use Tqdev\PhpCrudApi\Middleware\Router\SimpleRouter;
use Tqdev\PhpCrudApi\Record\ErrorCode;
use Tqdev\PhpCrudApi\ResponseUtils;
use Tqdev\PhpCrudAdmin\Client\CrudApi;
use Tqdev\PhpCrudAdmin\Column\DefinitionService;
use Tqdev\PhpCrudAdmin\Controller\MultiResponder;
use Tqdev\PhpCrudAdmin\Controller\ColumnController;
use Tqdev\PhpCrudAdmin\Controller\TableController;
use Tqdev\PhpCrudAdmin\Column\ColumnService;
use Tqdev\PhpCrudAdmin\Column\TableService;
use Tqdev\PhpCrudAdmin\Client\CurlCaller;
use Tqdev\PhpCrudAdmin\Client\LocalCaller;

class Admin implements RequestHandlerInterface
{
    private $router;
    private $responder;
    private $debug;

    public function __construct(Config $config)
    {
        $caller = new LocalCaller($config->getApi());
        if ($config->getUrl()) {
            $caller = new CurlCaller($config->getUrl());
        }
        $api = new CrudApi($caller);
        $prefix = sprintf('PhpCrudAdmin-%s-%s-', substr(md5($config->getUrl()), 0, 12), substr(md5(__FILE__), 0, 12));
        $cache = CacheFactory::create($config->getCacheType(), $prefix, $config->getCachePath());
        $definition = new DefinitionService($api);
        $responder = new MultiResponder($config->getTemplatePath());
        $router = new SimpleRouter($config->getBasePath(), $responder, $cache, $config->getCacheTime(), $config->getDebug());
        $responder->setVariable('base', $router->getBasePath());
        $responder->setVariable('menu', $definition->getTableNames());
        $responder->setVariable('table', '');
        foreach ($config->getControllers() as $controller) {
            switch ($controller) {
                case 'columns':
                    $columns = new ColumnService($api, $definition);
                    new ColumnController($router, $responder, $columns);
                    break;
                case 'tables':
                    $tables = new TableService($api, $definition);
                    $controller = new TableController($router, $responder, $tables);
                    $router->register('GET', '/', array($controller, '_list'));
                    break;
            }
        }
        $this->router = $router;
        $this->responder = $responder;
        $this->debug = $config->getDebug();
    }

    private function addParsedBody(ServerRequestInterface $request): ServerRequestInterface
    {
        $body = $request->getBody();
        if ($body->isReadable() && $body->isSeekable()) {
            $contents = $body->getContents();
            $body->rewind();
            if ($contents) {
                parse_str($contents, $parsedBody);
                $request = $request->withParsedBody($parsedBody);
            }
        }
        return $request;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = null;
        try {
            $response = $this->router->route($this->addParsedBody($request));
        } catch (\Throwable $e) {
            $response = $this->responder->error(ErrorCode::ERROR_NOT_FOUND, $e->getMessage());
            if ($this->debug) {
                $response = ResponseUtils::addExceptionHeaders($response, $e);
            }
        }
        return $response;
    }
}
