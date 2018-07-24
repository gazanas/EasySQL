<?php

namespace EasySQL\Src\Data;

class Parameters
{

    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $query     The query to be executed.
     * @param array  $params    The parameters array passed by the user.
     *
     * @return array|null       The prepared parameters for execution.
     */
    public function prepareParameters(string $query, $params)
    {
        $bindParams = array();

        if(is_array($params) === false || empty($params) === true)
            return null;

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
            $bindParams = array_merge($bindParams, array_values($this->extractParameters($params)));
        }

        // Put the type string in the first position of the parameters.
        return $bindParams;
    }

    /**
    * Sort the parameters array in accordance to the column order in the table.
    *
    * @param array $params          The parameters array passed by the user.
    * @param string $set            Table name.
    *
    * @return array $bindParams     The completed sorted parameters array.
    */
    private function sortParameters(array $params, string $set)
    {

        $bindParams = array();

        $dbinfo = new \EasySQL\Src\API\DatabaseDAO($this->db);

        // Get the columns of table.
        $fields = $dbinfo->getColumns($set);
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

        return array_values(array_filter($bindParams, array($this, 'parameterFilter')));
    }
    
    private function parameterFilter($param){
        return ($param !== NULL && $param !== FALSE && $param !== '');
    }

    /**
    * Extracts all the parameters from the api call
    *
    * @param $params        The parameters array passed by the user.
    *
    * @retrun $bindParams   The extracted parameters array.
    */
    private function extractParameters(array $params) {

        $bindParams = array();

        // Set the parameters array in order of the column fields.
        foreach ($params as $field => $param) {
            if (is_array($param) === true) {
                if(isset(array_values($param)[1])) {
                    $bindParams[] = array_values($param)[1];
                } else {
                    $bindParams[] = array_values($param)[0];
                }
            } else {
                $bindParams[] = $param;
            }
        }

        return $bindParams;
    }
}
