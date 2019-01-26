<?php

namespace EasySQL\Src\Parameters;

class UpdateParameters extends Parameters {

	protected $sets;

    /**
    * Intializes the parameters object.
    *
    * @param \EasySQL\Src\Sets\Sets $sets   The sets object.
    */
    public function __construct(\EasySQL\Src\Sets\Sets $sets) {
        $this->sets = $sets;
    }
	
    /**
    * Checks if the required parameters are passed.
    * Extracts all the parameters from the api call
    *
    * @param $params        The parameters array passed by the user.
    *
    * @return array         The extracted parameters array.
    */
    protected function extractParameters(array $params, string $table = null) {

        $this->matchRequired($params);

        $bindParams[] = $params['updated'];
        $params = array_diff_key($params, array('to_update' => 1, 'updated' => 1));

        foreach ($params as $field => $param) {
            if (is_array($param)) {
                if(count($param) > 2) {
                    throw new \Exception('Parameter array '.preg_replace('/\,\s*\)$/', ')', var_export($param, true)).' can contain maximum two parameters.');
                } else if(count($param) == 2) {
                    if(array_key_exists('operator', $param)) {
                        unset($param['operator']);
                    } else {
                        throw new \Exception('Parameter array '.preg_replace('/\,\s*\)$/', ')', var_export($param, true)).' with two parameters must contain an operator');
                    }
                }
                
                $this->checkFieldExists(key($param), $table);
                $param = array_values($param)[0];
            } else {
                $this->checkFieldExists($field, $table);
            }

            if(!is_string($param) && !is_numeric($param))
                throw new \Exception('Parameter: '.preg_replace('/\,\s*\)$/', ')', var_export($param, true)).' is not valid.');
            
            $bindParams[] = $param;
        }

        return array_values($bindParams);
    }

    /**
     *   Finds the parameters that are necessary for the API call and
     *   check if these exist in the parameters passed by the user, if not then throws a Required Exception
     *
     * @param array  $params    The parameters array passed by the user.
     *
     * @throws \Exception        The required parameters were not found.
     */
    private function matchRequired(array $params) {

        if (array_key_exists('to_update', $params) === false)
            throw new \Exception('Missing Required Field (to_update)');
    
        if(array_key_exists('updated', $params) === false) 
            throw new \Exception('Missing Required Field (updated)');
    }

}