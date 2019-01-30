<?php

namespace EasySQL\Src\Clause;

interface ClauseInterface
{
    public function prepareClause(array $params);
}
