<?php

namespace Tqdev\PhpCrudAdmin\Column;

use Tqdev\PhpCrudAdmin\Client\CrudApi;
use Tqdev\PhpCrudAdmin\Column\DefinitionService;
use Tqdev\PhpCrudAdmin\Document\TemplateDocument;
use Tqdev\PhpCrudAdmin\Document\CsvDocument;

class ColumnService
{
    private $api;
    private $definition;

    public function __construct(CrudApi $api, DefinitionService $definition)
    {
        $this->api = $api;
        $this->definition = $definition;
    }

    public function hasTable(string $table, string $action): bool
    {
        return $this->definition->hasTable($table, $action);
    }

    private function getDataTypes(): array
    {
        $types = array('bigint', 'bit', 'blob', 'boolean', 'clob', 'date', 'decimal', 'double', 'float', 'geometry', 'integer', 'time', 'timestamp', 'varbinary', 'varchar');
        return array_combine($types, $types);
    }

    private function getTableNames(): array
    {
        $tables = $this->definition->getTableNames();
        return array_combine($tables, $tables);
    }

    private function getBooleanValues(): array
    {
        return array('no', 'yes');
    }

    private function getDropDownValues(string $relatedTable): array
    {
        $values = array();
        if ($relatedTable) {
            $pair = $this->definition->getColumnPair($relatedTable);
            $args = array('include' => implode(',', $pair));
            $data = $this->api->listRecords($relatedTable, $args);
            foreach ($data['records'] as $record) {
                if (count($pair) > 1) {
                    $values[$record[$pair[0]]] = $record[$pair[1]];
                } else {
                    $values[$record[$pair[0]]] = $record[$pair[0]];
                }
            }
        }
        return $values;
    }

    public function createForm(string $table, string $action): TemplateDocument
    {
        $types = $this->getDataTypes();
        $primaryKey = $this->definition->getPrimaryKey($table, $action);

        $columns = $this->definition->getColumns($table, $action);

        foreach ($columns as $i => $column) {
            $values = $this->getDropDownValues($references[$column]);
            $columns[$i] = array('name' => $column, 'values' => $values);
        }

        $variables = array(
            'table' => $table,
            'action' => $action,
            'columns' => $columns,
            'primaryKey' => $primaryKey,
        );

        return new TemplateDocument('layouts/default', 'column/create', $variables);
    }

    public function create(string $table, string $action, /* object */ $record): TemplateDocument
    {
        $primaryKey = $this->definition->getPrimaryKey($table, $action);

        $name = $this->api->createRecord($table, $record);

        $variables = array(
            'table' => $table,
            'action' => $action,
            'id' => $name,
            'primaryKey' => $primaryKey,
        );

        return new TemplateDocument('layouts/default', 'column/created', $variables);
    }

    public function read(string $table, string $action, string $name): TemplateDocument
    {
        $column = $this->definition->getColumn($table, $name);

        $variables = array(
            'table' => $table,
            'action' => $action,
            'name' => $name,
            'column' => $column,
        );

        return new TemplateDocument('layouts/default', 'column/read', $variables);
    }

    public function updateForm(string $table, string $action, string $name): TemplateDocument
    {
        $column = $this->definition->getColumn($table, $name);

        foreach ($column as $key => $value) {
            $column[$key] = array('value' => $value, 'values' => array());
            switch ($key) {
                case 'type':
                    $column[$key]['values'] = $this->getDataTypes();
                    break;
                case 'nullable':
                case 'pk':
                    $column[$key]['values'] = $this->getBooleanValues();
                    break;
                case 'fk':
                    $column[$key]['values'] = $this->getTableNames();
                    break;
            }
        }

        $variables = array(
            'table' => $table,
            'action' => $action,
            'name' => $name,
            'column' => $column,
        );

        return new TemplateDocument('layouts/default', 'column/update', $variables);
    }

    public function update(string $table, string $action, string $name, /* object */ $record): TemplateDocument
    {
        $primaryKey = $this->definition->getPrimaryKey($table, $action);

        $affected = $this->api->updateRecord($table, $name, $record);

        $variables = array(
            'table' => $table,
            'action' => $action,
            'id' => $name,
            'primaryKey' => $primaryKey,
            'affected' => $affected,
        );

        return new TemplateDocument('layouts/default', 'column/updated', $variables);
    }

    public function deleteForm(string $table, string $action, string $name): TemplateDocument
    {
        $primaryKey = $this->definition->getPrimaryKey($table, 'read');

        $record = $this->api->readRecord($table, $name, []);

        $name = $this->definition->referenceText($table, $record);

        $variables = array(
            'table' => $table,
            'action' => $action,
            'id' => $name,
            'primaryKey' => $primaryKey,
            'name' => $name,
        );

        return new TemplateDocument('layouts/default', 'column/delete', $variables);
    }

    public function delete(string $table, string $action, string $name): TemplateDocument
    {
        $primaryKey = $this->definition->getPrimaryKey($table, 'read');

        $affected = $this->api->deleteRecord($table, $name);

        $variables = array(
            'table' => $table,
            'action' => $action,
            'id' => $name,
            'primaryKey' => $primaryKey,
            'affected' => $affected,
        );

        return new TemplateDocument('layouts/default', 'column/deleted', $variables);
    }

    public function _list(string $table, string $action): TemplateDocument
    {
        $data = $this->definition->getTable($table);

        $variables = array(
            'table' => $table,
            'action' => $action,
            'columns' => $data['columns'],
        );

        return new TemplateDocument('layouts/default', 'column/list', $variables);
    }
}
