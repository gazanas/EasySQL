<?php

namespace Src\API;

use Src\Collection\Collection as Collection;

class APICall
{

    /**
    * If a parameter is required but is not passed set it to NULL
    *
    * @param array $params The parameters array.
    * @param string $table The name of the table.
    *
    * @return array The complete parameters array.
    */
    public function setNotPassedParameters(array $params, string $table)
    {
        $nulled = array();
        $columns = $this->dbinfo->getNullableColumns($this->config[4], $table);
        foreach ($columns as $column) {
            if (isset($params[$column]) === false || empty($params[$column])) {
                $nulled[$column] = null;
            }
        }

        return array_merge($params, $nulled);
    }
    
    /**
     *   Finds the parameters that are necessary for the API call and
     *   check if these exist in the parameters passed by the user, if not then throws a Required Exception
     *
     * @param string $database The database name.
     * @param array  $params   The array of the actual parameters passed by the user.
     * @param string $table    The table name.
     * @param array  $config   The Database Configuration.
     *
     * @return boolean
     *
     * @throws RequiredException The required parameters were not found.
     */
    public function matchRequired(string $database, array $params, string $table, array $config)
    {
        $required = $this->dbinfo->getRequiredColumns($database, $table);

        if (empty(array_diff_key($required, $params)) === false) {
            $error = $this->setUpError($required, $params);

            throw new RequiredException($error);
        }

        return true;
    }


    /**
     * The user api call contains the required action for the query
     *
     * @param array  $required The required action parameter for the query.
     * @param array  $params   The params passed by the user.
     * @param string $table    The name of the table.
     * @param array  $config   The database configuration.
     *
     * @return boolean
     *
     * @throws RequiredException The required action parameter was not found.
     */
    public function matchRequiredAction(array $required, array $params, string $table, array $config)
    {
        
        $swappedRequired = [];
        
        $notRequired     = $this->dbinfo->getAutoCompleted($table);
        
        foreach ($notRequired as $item) {
            $key = array_search($item, $required);
            if ($key === true) {
                unset($required[$key]);
            }
        }

        foreach ($required as $key => $value) {
            $swappedRequired[$value] = $required[$key];
        }

        if (empty(array_diff_key($swappedRequired, $params)) === false) {
            $error = $this->setUpError($swappedRequired, $params);

            throw new RequiredException($error);
        }

        return true;
    }

    /**
    * Set up the error message for the missing required parameters.
    *
    * @param array $swappedRequired The required parameters array in swapped order.
    * @param array $params The parameters array.
    *
    * @return string $error The error message.
    */
    private function setUpError(array $swappedRequired, array $params)
    {
        
        $missing = array_diff_key($swappedRequired, $params);
        $error   = 'Missing Required Fields (';
        $i       = 0;
        foreach ($missing as $item) {
            if ($i === (count($missing) - 1)) {
                $error .= $item.')';
                break;
            }

            $error .= $item.', ';
            $i++;
        }

        return $error;
    }
}
