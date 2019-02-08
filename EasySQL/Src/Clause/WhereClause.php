<?php

namespace EasySQL\Src\Clause;

use EasySQL\Src\Clause\Exceptions\InvalidConditionException;
use EasySQL\Src\Clause\Exceptions\InvalidOperatorException;
use EasySQL\Src\Sets\Sets;

class WhereClause implements ClauseInterface
{
    /**
     * Simple Where Clause formats an array that consists of parameters
     * in a where clause where all conditions are equalities and are
     * connected with an AND expression.
     *
     * @param array $params The parameters array passed by the user.
     *
     * @return string|null      The where clause of the SQL query.
     */
    public function prepareClause(array $params)
    {
        $query = ' WHERE '.$params[0];

        $query = preg_replace(
            '/([A-Za-z0-9_]+) (\=|\<\>|\>|\<|\>\=|\<\=) (\\\'(\s*\w*\s*)+\\\'|\d+)/',
            '$1 $2 ?',
            $query
        );

        return $query;
    }
}
