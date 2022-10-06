<?php

namespace Tqdev\PhpCrudAdmin\Controller;

use Psr\Http\Message\ResponseInterface;
use Tqdev\PhpCrudApi\Controller\Responder;
use Tqdev\PhpCrudApi\Record\Document\ErrorDocument;
use Tqdev\PhpCrudApi\Record\ErrorCode;
use Tqdev\PhpCrudApi\ResponseFactory;
use Tqdev\PhpCrudAdmin\Document\TemplateDocument;
use Tqdev\PhpCrudAdmin\Document\CsvDocument;

class MultiResponder implements Responder
{
    private $variables;
    private $templatePath;

    public function __construct(string $templatePath)
    {
        $this->variables = array();
        $this->templatePath = $templatePath;
    }

    public function setVariable(string $name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function error(int $error, string $argument, $details = null): ResponseInterface
    {
        $errorCode = new ErrorCode($error);
        $status = $errorCode->getStatus();
        $document = new ErrorDocument($errorCode, $argument, $details);
        $result = new TemplateDocument('layouts/error', 'error/show', $document->serialize());
        $result->addVariables($this->variables);
        $result->setTemplatePath($this->templatePath);
        return ResponseFactory::fromHtml($status, (string) $result);
    }

    public function success($result): ResponseInterface
    {
        if ($result instanceof CsvDocument) {
            return ResponseFactory::fromCsv(ResponseFactory::OK, (string) $result);
        } elseif ($result instanceof TemplateDocument) {
            $result->addVariables($this->variables);
            $result->setTemplatePath($this->templatePath);
            return ResponseFactory::fromHtml(ResponseFactory::OK, (string) $result);
        } else {
            throw new \Exception('Document type not supported: ' . get_class($result));
        }
    }

    public function multi($results): ResponseInterface
    {
        return ResponseFactory::fromHtml(ResponseFactory::OK, (string) "Not supported");
    }

    public function exception($exception): ResponseInterface
    {
        return ResponseFactory::fromHtml(ResponseFactory::INTERNAL_SERVER_ERROR, (string) $exception);
    }
}
