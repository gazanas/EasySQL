<?php

namespace EasySQL\Src\Parameters;

use EasySQL\Src\Parameters\Exceptions\InvalidParameterException;

class WhereParameters extends Parameters
{
    
    /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $table
     * @param array $params
     *
     * @return array $bind_params   The prepared parameters for execution.
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
     * @param array $params
     * @param string $table
     *
     * @return array         The extracted parameters array.
     */
    protected function extractParameters(array $params, string $table)
    {
        $bindParams = array();
        
        foreach ($params as $field => $param) {
            if (is_array($param)) {
                $field = key($param);
                $param = $param[$field];
            }
            if (!in_array($field, $this->sets->getActionParameters())) {
                $this->checkFieldExists($field, $table);
            }

            if (!is_string($param) && !is_numeric($param)) {
                throw new InvalidParameterException(var_export($param, true));
            }

            $bindParams[] = $param;
        }
        
        return array_values($bindParams);
    }
}
