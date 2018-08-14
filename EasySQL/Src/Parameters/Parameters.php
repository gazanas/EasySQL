<?php

namespace EasySQL\Src\Parameters;

class Parameters {
	
    protected $sets;

    /**
    * Intializes the parameters object.
    *
    * @param string $set        The table name.
    */
    public function __construct($sets) {
        $this->sets = $sets;
    }

	 /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $action        The query action to be executed.
     * @param string $table         The name of the table.
     * @param array  $params        The parameters array passed by the user.
     *
     * @return array $bind_params   The prepared parameters for execution.
     * 
     * @throws \Exception           Wrong type of parameter passed.
     */
    public function prepareParameters(string $action, string $table, $params)
    {
        if(is_array($params) === false)
            throw new \Exception('Wrong parameter type passed.');

        $parameterValidation = new ParametersValidation();
        $parameterValidation->matchRequired($params, $this->getRequiredParameters($action, $table));
        
        $params = array_diff_key($params, array('options' => 1, 'condition' => 1, 'return' => 1, 'to_update' => 1));

        $bindParams = $this->extractParameters($params);
        
        return $bindParams;
    }

    /**
    * Returns required parameters for the query action.
    *
    * @param string $action     The query action to be executed.
    * @param string $table      The table name.
    *
    * @return array $required   The required parameters array.
    */
    private function getRequiredParameters($action, $table) {
        switch($action) {
            case 'update':
                $required = array('to_update', 'updated');
                break;
            case 'value':
                $required = array('return');
                break;
            case 'insert':
                $required = $this->sets->getRequiredColumns($table);
                break;
            default:
                $required = array();
        }

        return $required;
    }

    /**
    * Checks if the required parameters are passed.
    * Extracts all the parameters from the api call
    *
    * @param $params        The parameters array passed by the user.
    *
    * @return array         The extracted parameters array.
    */
    private function extractParameters(array $params) {
        
        $bindParams = array();

        foreach ($params as $field => $param) {
            if (is_array($param)) {
                    $param = array_values($param)[count($param)-1];
            }
                $bindParams[] = $param;
        }

        return array_values($bindParams);
    }
}