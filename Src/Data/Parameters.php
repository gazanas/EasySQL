<?php

namespace Src\Data;

class Parameters
{

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $query  The query to be executed.
     * @param array  $params The parameters passed by the user.
     *
     * @return array|void The prepared parameters for execution.
     */
    public function prepareParameters(string $query, $params)
    {
        $bindParams = array();
        
        if (is_array($params) === false || isset($params) === false || empty($params) === true) {
            return;
        }

        if (isset($params['updated']) === true) {
            $bindParams = [&$params['updated']];
        }

        if (isset($params['condition']) === true && empty($params['condition']) === false) {
            unset($params['condition']);
        }

        // Get the table name.
        preg_match(
            '/(SELECT (\*|[A-Za-z0-9_]+)?|DELETE|UPDATE|INSERT)( FROM| INTO)?( [A-Za-z0-9_]+)/',
            $query,
            $result
        );

        // Table name.
        $tableName = $result[(count($result) - 1)];

        if(preg_match('/INSERT INTO/', $query)) {
            $bindParams = $this->sortParameters($params, $tableName);
        } else {
            $bindParams = array_merge($bindParams, array_values($params));
        }

        // Put the type string in the first position of the parameters.
        return $bindParams;
    }

    /**
    * Sort the parameters array in accordance to the column order in the table.
    *
    * @param array $params The parameters array.
    * @param string $tableName The name of the table.
    *
    * @return array $bindParams The completed sorted parameters array.
    */
    private function sortParameters(array $params, string $tableName)
    {

        $bindParams = array();

        $dbinfo = new \Src\API\DatabaseDAO($this->config);

        // Get the columns of table.
        $fields = $dbinfo->getColumns($tableName);
        // Set the parameters array in order of the column fields.
        foreach ($params as $field => $param) {
            if (is_array($param) === true) {
                foreach ($param as $key => $value) {
                    if (in_array($key, $fields) === true) {
                        $bindParams[] = $value;
                    }
                }
            } else {
                if (in_array($field, $fields) === true) {
                    $bindParams[] = $param;
                }
            }
        }

        ksort($bindParams);

        return $bindParams;
    }
}
