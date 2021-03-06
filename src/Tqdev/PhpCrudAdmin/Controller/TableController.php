<?php

namespace Tqdev\PhpCrudAdmin\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tqdev\PhpCrudApi\Controller\Responder;
use Tqdev\PhpCrudApi\Middleware\Router\Router;
use Tqdev\PhpCrudApi\Record\ErrorCode;
use Tqdev\PhpCrudApi\RequestUtils;
use Tqdev\PhpCrudAdmin\Column\TableService;

class TableController
{
    private $service;
    private $responder;

    public function __construct(Router $router, Responder $responder, TableService $service)
    {
        $router->register('GET', '/admin/table/create', array($this, 'createForm'));
        $router->register('POST', '/admin/table/create', array($this, 'create'));
        $router->register('GET', '/admin/table/delete/*', array($this, 'deleteForm'));
        $router->register('POST', '/admin/table/delete/*', array($this, 'delete'));
        $router->register('GET', '/admin/table/list', array($this, '_list'));
        $this->service = $service;
        $this->responder = $responder;
    }

    public function createForm(ServerRequestInterface $request): ResponseInterface
    {
        $action = RequestUtils::getPathSegment($request, 3);
        $result = $this->service->createForm($action);
        return $this->responder->success($result);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $action = RequestUtils::getPathSegment($request, 3);
        $table = $request->getParsedBody();
        if ($table === null) {
            return $this->responder->error(ErrorCode::HTTP_MESSAGE_NOT_READABLE, '');
        }
        $table['columns'] = [["name" => "id", "type" => "bigint", "pk" => true]];
        $result = $this->service->create($action, $table);
        return $this->responder->success($result);
    }

    public function deleteForm(ServerRequestInterface $request): ResponseInterface
    {
        $action = RequestUtils::getPathSegment($request, 3);
        $name = RequestUtils::getPathSegment($request, 4);
        if (!$this->service->hasTable($name, 'read')) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $name);
        }
        $result = $this->service->deleteForm($action, $name);
        return $this->responder->success($result);
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $action = RequestUtils::getPathSegment($request, 3);
        $name = RequestUtils::getPathSegment($request, 4);
        if (!$this->service->hasTable($name, 'read')) {
            return $this->responder->error(ErrorCode::TABLE_NOT_FOUND, $name);
        }
        $result = $this->service->delete($action, $name);
        return $this->responder->success($result);
    }

    public function _list(ServerRequestInterface $request): ResponseInterface
    {
        $action = RequestUtils::getPathSegment($request, 3);
        $result = $this->service->_list($action);
        return $this->responder->success($result);
    }
}
