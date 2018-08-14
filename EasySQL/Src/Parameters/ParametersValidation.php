<?php

namespace EasySQL\Src\Parameters;

class ParametersValidation {
    
    /**
     *   Finds the parameters that are necessary for the API call and
     *   check if these exist in the parameters passed by the user, if not then throws a Required Exception
     *
     * @param array  $params    The parameters array passed by the user.
     * @param array $required   The required columns for the query.
     * @param string $set       Table name.
     *
     * @return boolean
     *
     * @throws \Exception        The required parameters were not found.
     */
    public function matchRequired(array $params, $required)
    {
        if ($this->compareArraysKeys($required, $params) === false) {
            $error = $this->setUpError($required, $params);

            throw new \Exception($error);
        }

        return true;
    }

    /**
    * Compare the values of one array by the keys of another.
    *
    * @param $keys_array    The array containing the keys.
    * @param $array         The associative array to search.
    *
    * @return boolean
    */
    private function compareArraysKeys($keys_array, $array) {
        foreach($keys_array as $key) {
            if(!array_key_exists($key, $array)) {
                return false;
            }
        }

        return true;
    }

    /**
    * Set up the error message for the missing required parameters.
    *
    * @param array $swappedRequired     The required parameters array in swapped order.
    * @param array $params              The parameters array passed by the user.
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