<?php

namespace Tqdev\PhpCrudAdmin\Client;

class CrudApi
{
    private $caller;

    public function __construct(ApiCaller $caller)
    {
        $this->caller = $caller;
    }

    public function readDatabase(array $args)
    {
        return $this->caller->call('GET', '/columns', $args);
    }

    public function readTable(string $table, array $args)
    {
        return $this->caller->call('GET', '/columns/' . rawurlencode($table), $args);
    }

    public function readColumn(string $table, string $column, array $args)
    {
        return $this->caller->call('GET', '/columns/' . rawurlencode($table) . '/' . rawurlencode($column), $args);
    }

    public function updateColumn(string $table, string $column, array $data)
    {
        return $this->caller->call('PUT', '/columns/' . rawurlencode($table) . '/' . rawurlencode($column), [], $data);
    }

    public function createColumn(string $table, array $data)
    {
        return $this->caller->call('POST', '/columns/' . rawurlencode($table), [], $data);
    }

    public function deleteColumn(string $table, string $column)
    {
        return $this->caller->call('DELETE', '/columns/' . rawurlencode($table) . '/' . rawurlencode($column), []);
    }

    public function createTable(array $data)
    {
        return $this->caller->call('POST', '/columns', [], $data);
    }

    public function deleteTable(string $table)
    {
        return $this->caller->call('DELETE', '/columns/' . rawurlencode($table), []);
    }
}
