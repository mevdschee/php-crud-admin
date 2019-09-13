<?php

namespace Tqdev\PhpCrudAdmin\Column;

use Tqdev\PhpCrudAdmin\Client\CrudApi;
use Tqdev\PhpCrudAdmin\Column\DefinitionService;
use Tqdev\PhpCrudAdmin\Document\TemplateDocument;

class TableService
{
    private $api;
    private $definition;

    public function __construct(CrudApi $api, DefinitionService $definition)
    {
        $this->api = $api;
        $this->definition = $definition;
    }

    public function hasTable(string $action): bool
    {
        return $this->definition->hasTable($action);
    }

    private function makeForm(array $table): array
    {
        $form = array();
        foreach ($table as $key => $value) {
            $form[$key] = array('value' => $value, 'type' => 'text', 'required' => false);
            switch ($key) {
                case 'name':
                    $form[$key]['required'] = true;
                    break;
            }
        }
        return $form;
    }

    public function createForm(string $action): TemplateDocument
    {
        $table = array('name' => '');

        $form = $this->makeForm($table);

        $variables = array(
            'action' => $action,
            'form' => $form,
        );

        return new TemplateDocument('layouts/default', 'table/create', $variables);
    }

    public function create(string $action, /* object */ $table): TemplateDocument
    {
        $success = $this->api->createTable($table);

        $variables = array(
            'action' => $action,
            'name' => $table['name'],
            'success' => $success,
        );

        return new TemplateDocument('layouts/default', 'table/created', $variables);
    }

    public function deleteForm(string $action, string $name): TemplateDocument
    {
        $table = $this->definition->getTable($name);

        $variables = array(
            'action' => $action,
            'table' => $table['name'],
        );

        return new TemplateDocument('layouts/default', 'table/delete', $variables);
    }

    public function delete(string $action, string $name): TemplateDocument
    {
        $success = $this->api->deleteTable($name);

        $variables = array(
            'action' => $action,
            'table' => $name,
            'success' => $success,
        );

        return new TemplateDocument('layouts/default', 'table/deleted', $variables);
    }

    public function _list(string $action): TemplateDocument
    {
        $tables = $this->definition->getTableNames();

        $variables = array(
            'action' => $action,
            'tables' => $tables,
        );

        return new TemplateDocument('layouts/default', 'table/list', $variables);
    }
}
