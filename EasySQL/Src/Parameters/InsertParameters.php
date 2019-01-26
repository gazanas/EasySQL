<?php

namespace EasySQL\Src\Parameters;

use EasySQL\Src\Parameters\Exceptions\MissingRequiredFieldsException;

class InsertParameters extends Parameters
{
    /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $table          The name of the table.
     * @param array $params          The parameters array passed by the user.
     *
     * @return array $bindRarams     The prepared parameters for execution.
     */
    public function prepareParameters(string $table, array $params)
    {
                
        $bindParams = $this->extractParameters($params, $table);

        return $bindParams;
    }

    /**
     * Checks if the required parameters are passed.
     * Extracts all the parameters from the api call
     *
     * @param $params        Parameters array passed by the user.
     * @param $table         
     *
     * @return array         The extracted parameters array.
     */
    private function extractParameters(array $params, string $table)
    {

        foreach (array_keys($params) as $field) {
            $this->checkFieldExists($field, $table);
        }

        $this->matchRequired($params, array_flip($this->sets->getRequiredColumns($table)));

        return array_values($params);
    }

    /**
     * Finds the parameters that are necessary for the API call and
     * checks if these exist in the parameters passed by the user, if not then throws a Required Exception
     *
     * @param array $params   The parameters array passed by the user.
     * @param array $required The required columns for the query.
     */
    private function matchRequired(array $params, array $required)
    {
        $missing = '';
        $flag = false;

        foreach(array_keys($required) as $field) {
            if (array_key_exists($field, $params) === false) {
                $missing .= $field.',';
                $flag = true;
            }
        }

        if($flag) {
            throw new MissingRequiredFieldsException($missing);
        }
    }
}
