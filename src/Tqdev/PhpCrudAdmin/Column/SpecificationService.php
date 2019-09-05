<?php

namespace Tqdev\PhpCrudAdmin\Column;

use Tqdev\PhpCrudAdmin\Client\CrudApi;

class SpecificationService
{
    private $api;
    private $database;

    public function __construct(CrudApi $api)
    {
        $this->api = $api;
        $this->database = $this->api->readDatabase(array());
        $this->properties = array();
    }

    public function hasTable(string $tableName): bool
    {
        if (isset($this->database['tables'])) {
            foreach ($this->database['tables'] as $table) {
                if ($table['name']==$tableName) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getReferenced(string $table, string $action)
    {
        $properties = $this->getProperties($table, $action);

        $referenced = array();
        foreach ($properties as $field => $property) {
            if (isset($property['x-referenced'])) {
                $referenced = array_merge($referenced, $property['x-referenced']);
            }
        }
        for ($i = 0; $i < count($referenced); $i++) {
            $referenced[$i] = explode('.', $referenced[$i]);
        }
        return $referenced;
    }

    public function getPrimaryKey(string $table, string $action)
    {
        $properties = $this->getProperties($table, $action);

        foreach ($properties as $field => $property) {
            if (isset($property['x-primary-key'])) {
                return $field;
            }
        }
        return false;
    }

    private function getDisplayColumn($columns)
    {
        // TODO: make configurable
        $names = array('name', 'title', 'description', 'username');
        foreach ($names as $name) {
            if (in_array($name, $columns)) {
                return $name;
            }
        }
        return $columns[0];
    }

    public function getColumnPair(string $table)
    {
        $primaryKey = $this->getPrimaryKey($table, 'list');
        $columns = $this->getColumns($table, 'list');
        $displayColumn = $this->getDisplayColumn($columns);
        return array($primaryKey, $displayColumn);
    }

    public function getColumns(string $table, string $action): array
    {
        $properties = $this->getProperties($table, $action);
        return array_keys($properties);
    }

    public function getMenu()
    {
        $items = array();
        if (isset($this->database['tables'])) {
            foreach ($this->database['tables'] as $table) {
                array_push($items, $table['name']);
            }
        }
        return $items;
    }


    public function referenceText(string $table, /* object */ $record)
    {
        $properties = $this->getProperties($table, 'read');
        $displayColumn = $this->getDisplayColumn(array_keys($properties));
        return $record[$displayColumn];
    }

    public function referenceId(string $table, /* object */ $record)
    {
        $primaryKey = $this->getPrimaryKey($table, 'read');
        return $record[$primaryKey];
    }
}
