<?php

namespace EasySQL\Src\Data;

class InsertQuery {
	
	/**
     * Gets the parameters array for the insert query and sets up the
     * values clause from the values of this array.
     *
     * @param array $allColumns  All the columns of the table.
     * @param array $restColumns The columns that are not auto completed.
     * @param array $autoColumns The array of all the columns that have auto completed values.
     * @param array $nullabelColumns The array of all the columns that can have null value.
     *
     * @return string $query The insert query.
     */
    public function setUpInsertQuery(array $allColumns, $restColumns, array $autoColumns, array $nullableColumns)
    {
        $preparedParameter = $this->getRequiredParametersToInsert($allColumns, $restColumns);
        $preparedParameter = $this->setNotPassedParameters($preparedParameter, $allColumns, $nullableColumns);

        $i     = 0;
        $query = '';
        foreach ($preparedParameter as $key => $parameter) {
            if ($i == (count($preparedParameter) - 1)) {
                if ($parameter === null) {
                    if ($this->checkCurrentTimestamp($key, $autoColumns)) {
                        $query .= 'NOW())';
                    } else {
                        $query .= 'NULL)';
                    }
                } else {
                    $query .= '?)';
                }
            } else {
                if ($parameter === null) {
                    if ($this->checkCurrentTimestamp($key, $autoColumns)) {
                        $query .= 'NOW(),';
                    } else {
                        $query .= 'NULL,';
                    }
                } else {
                    $query .= '?,';
                }
            }

            $i++;
        }

        return $query;
    }

    /**
    * If a parameter is required but is not passed set it to NULL
    *
    * @param array $params The parameters array.
    * @param array $allColumns The array of all the tables columns.
    * @param array $nullabelColumns The array of all the columns that can have null value.
    *
    * @return array The complete parameters array.
    */
    public function setNotPassedParameters(array $params, array $allColumns, $nullableColumns)
    {
        $new = array();
        foreach ($nullableColumns as $column) {
            if (isset($params[$column]) === false || empty($params[$column])) {
                $params[$column] = null;
            }
        }

        foreach($allColumns as $key => $column) {
		if(!isset($params[$column]))
               		$params[$column] = null;
            $new[$column] = $params[$column];

        }

        return $new;
    }

    /**
    * Check if a parameter is of current_timestamp type.
    *
    * @param string $field The column name.
    * @param array $autoColumns The array of the auto completed columns.
    *
    * @return boolean
    */
    public function checkCurrentTimestamp(string $field, array $autoColumns)
    {
        foreach ($autoColumns as $column) {
            if ($column['name'] == $field && $column['type'] == 'current_timestamp') {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets all the columns passed by the user for the insert value clause, checks if those that are missing
     * are auto_completed or nullable and gives them a value of NULL (string). Finally sets up the parameters array
     * for the insert value clause e.g. array('NULL', '?', '?', '?') say the first column is auto incremented.
     *
     * @param array $allColumns  All the columns from a table.
     * @param array $restColumns The columns that are not auto completed.
     *
     * @return array $preparedParameter An array that contains the prepared values of the insert values() clause.
     */
    private function getRequiredParametersToInsert(array $allColumns, array $restColumns)
    {
        $preparedParameter = array();
        
        foreach ($allColumns as $key => $column) {
            if (array_key_exists($column, $restColumns) === false) {
                if (empty(array_slice($restColumns, 0, $key)) === true) {
                    $restColumns = ([$column => null] + $restColumns);
                } else {
                    $preparedParameter = (array_slice($restColumns, 0, $key) + [$column => null] + array_slice($restColumns, $key, (count($restColumns) - 1)));
                }
            } else {
                $preparedParameter[$column] = $restColumns[$column]; 
            }
        }

        return $preparedParameter;
    }
}
