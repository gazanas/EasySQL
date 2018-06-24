<?php

namespace EasySQL\Src\API;

interface DAOInterface
{


    public function get(array $params = null);


    public function insert(array $params);


    public function value(array $params);


    public function update(array $params);


    public function delete(array $params = null);
}
