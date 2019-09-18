<?php

namespace EasySQL\Drivers;

abstract class DAO
{
    /**
     * Executes a query
     *
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    abstract public function executeQuery(string $query, array $params);
}
