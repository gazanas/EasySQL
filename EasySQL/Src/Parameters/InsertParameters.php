<?php

namespace EasySQL\Src\Parameters;

<<<<<<< HEAD
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
=======
class InsertParameters extends Parameters {

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
    * @param $table         The table name.
    *
    * @return array         The extracted parameters array.
    */
    protected function extractParameters(array $params, string $table = null) {

        foreach ($params as $field => $param) {
            if (is_array($param))
                throw new \Exception('Insert parameters can not include nested arrays '.var_export($param, true));
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
            $this->checkFieldExists($field, $table);
        }

        $this->matchRequired($params, array_flip($this->sets->getRequiredColumns($table)));

        return array_values($params);
    }

    /**
<<<<<<< HEAD
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
=======
     *   Finds the parameters that are necessary for the API call and
     *   check if these exist in the parameters passed by the user, if not then throws a Required Exception
     *
     * @param array  $params    The parameters array passed by the user.
     * @param array $required   The required columns for the query.
     *
     * @throws \Exception        The required parameters were not found.
     */
    private function matchRequired(array $params, $required) {

        $error   = 'Missing Required Fields (';
        $flag = false;

        foreach($required as $field => $param) {
            if (array_key_exists($field, $params) === false) {
                $error .= $field.',';
                $flag = true;
            }
        }
        
        $error = preg_replace('/\,$/', ')', $error);

        if($flag)
            throw new \Exception($error);
    }

}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
