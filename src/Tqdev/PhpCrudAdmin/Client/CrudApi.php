<?php

namespace Tqdev\PhpCrudAdmin\Client;

class CrudApi
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function readDatabase(array $args)
    {
        return $this->call('GET', '/columns', $args);
    }

    public function readTable(string $table, array $args)
    {
        return $this->call('GET', '/columns/' . rawurlencode($table), $args);
    }

    public function readColumn(string $table, string $column, array $args)
    {
        return $this->call('GET', '/columns/' . rawurlencode($table) . '/' . rawurlencode($column), $args);
    }

    public function updateColumn(string $table, string $column, array $data)
    {
        return $this->call('PUT', '/columns/' . rawurlencode($table) . '/' . rawurlencode($column), [], $data);
    }

    public function createColumn(string $table, array $data)
    {
        return $this->call('POST', '/columns/' . rawurlencode($table), [], $data);
    }

    public function deleteColumn(string $table, string $column)
    {
        return $this->call('DELETE', '/columns/' . rawurlencode($table) . '/' . rawurlencode($column), []);
    }

    private function call(string $method, string $path, array $args = [], $data = false)
    {
        $query = rtrim('?' . preg_replace('|%5B[0-9]+%5D|', '', http_build_query($args)), '?');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_URL, $this->url . $path . $query);
        if ($data) {
            $content = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($content);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
