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
                if ($table['name'] == $tableName) {
                    return true;
                }
            }
        }
        return false;
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
}
