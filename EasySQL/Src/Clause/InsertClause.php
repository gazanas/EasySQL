<?php

namespace EasySQL\Src\Clause;

class InsertClause implements ClauseInterface
{
    
    protected $autoColumns;

    /**
     * Initializes the insert clause object.
     *
     * @param array $autoColumns The table columns that have auto completed values.
     */
    public function __construct($autoColumns)
    {
          $this->autoColumns = $autoColumns;
    }

    /**
     * Returns the prepared insert clause for the query.
     *
     * @param array $params The parameters array passed by the user. 
     *
     * @return string $preparedClause    The prepared insert clause.
     */
    public function prepareClause($params)
    {
        $query = '';

        foreach ($params as $parameter) {
            if ($parameter === null) {
                $query .= 'NULL,';
            } else {
                $query .= '?,';
            }
        }

        $query = preg_replace('/\,$/', ')', $query);

        return $query;
    }
}
