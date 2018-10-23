<?php

namespace EasySQL\Src\Parameters;

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
            $this->checkFieldExists($field, $table);
        }

        $this->matchRequired($params, array_flip($this->sets->getRequiredColumns($table)));

        return array_values($params);
    }

    /**
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