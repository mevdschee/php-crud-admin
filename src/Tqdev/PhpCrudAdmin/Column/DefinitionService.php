<?php

namespace Tqdev\PhpCrudAdmin\Column;

use Tqdev\PhpCrudAdmin\Client\CrudApi;

class DefinitionService
{
    private $api;
    private $database;

    public function __construct(CrudApi $api)
    {
        $this->api = $api;
        $this->database = $this->optimizeDatabase($this->api->readDatabase(array()));
        $this->properties = array();
    }

    private function getColumnFields(): array
    {
        return array('name', 'type', 'length', 'precision', 'scale', 'nullable', 'pk', 'fk');
    }

    private function fillSparse(array $array): array
    {
        $full = array();
        $keys = $this->getColumnFields();
        foreach ($keys as $key) {
            if (!key_exists($key, $array)) {
                $full[$key] = null;
            } else {
                $full[$key] = $array[$key];
            }
        }
        return $full;
    }

    private function fillAllSparse(array $array): array
    {
        $full = array();
        foreach ($array as $key => $value) {
            $full[$key] = $this->fillSparse($value);
        }
        return $full;
    }

    private function optimizeDatabase($database)
    {
        $database['tables'] = $this->makeNamed($database['tables']);
        foreach ($database['tables'] as $name => $table) {
            $database['tables'][$name]['columns'] = $this->fillAllSparse($this->makeNamed($table['columns']));
        }
        return $database;
    }

    private function makeNamed(array $array)
    {
        $named = array();
        foreach ($array as $item) {
            foreach ($item as $key => $value) {
                if ($key == 'name') {
                    $named[$value] = $item;
                    break;
                }
            }
        }
        return $named;
    }

    public function hasTable(string $tableName): bool
    {
        return isset($this->database['tables'][$tableName]);
    }

    public function getTable(string $tableName)
    {
        return $this->database['tables'][$tableName];
    }

    public function getNewColumn()
    {
        return array_fill_keys($this->getColumnFields(), null);
    }

    public function getColumn(string $tableName, string $columnName)
    {
        return $this->database['tables'][$tableName]['columns'][$columnName];
    }

    public function getTableNames()
    {
        return array_keys(array_filter($this->database['tables'], function ($table) {
            return $table['type'] == 'table';
        }));
    }

    public function getViewNames()
    {
        return array_keys(array_filter($this->database['tables'], function ($table) {
            return $table['type'] == 'view';
        }));
    }
}
