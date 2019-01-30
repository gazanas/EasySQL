<?php

namespace EasySQL\Src\Parameters;

use EasySQL\Src\Parameters\Exceptions\InvalidParameterException;

class WhereParameters extends Parameters
{
    
    /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $table  The name of the table.
     * @param array  $params The parameters array passed by the user.
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
     * @param $params        Parameters array passed by the user.
     *
     * @return array         The extracted parameters array.
     */
    protected function extractParameters(array $params, string $table)
    {
        unset($params['condition']);
        $bindParams = array();
        
        foreach ($params as $field => $param) {
            if (is_array($param)) {
                if (count($param) > 2) {
                    throw new InvalidParameterException(var_export($param, true));
                } elseif (count($param) == 2) {
                    if (array_key_exists('operator', $param)) {
                        unset($param['operator']);
                    } else {
                        throw new InvalidParameterException(var_export($param, true));
                    }
                }
                
                $this->checkFieldExists(key($param), $table);
                $param = array_values($param)[0];
            } elseif ($field != 'updated') {
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
