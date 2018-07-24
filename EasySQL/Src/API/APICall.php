<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Collection\Collection as Collection;

class APICall
{
    
    /**
     *   Finds the parameters that are necessary for the API call and
     *   check if these exist in the parameters passed by the user, if not then throws a Required Exception
     *
     * @param array  $params    The parameters array passed by the user.
     * @param string $set       Table name.
     *
     * @return boolean
     *
     * @throws RequiredException The required parameters were not found.
     */
    public function matchRequired(array $params, string $set)
    {
        $required = $this->dbinfo->getRequiredColumns($set);

        if (empty(array_diff_key($required, $params)) === false) {
            $error = $this->setUpError($required, $params);

            throw new RequiredException($error);
        }

        return true;
    }


    /**
     * The user api call contains the required action for the query
     *
     * @param array  $required  The required action parameter for the query.
     * @param array  $params    The parameters array passed by the user.
     * @param string $set       Table name
     *
     * @return boolean
     *
     * @throws RequiredException The required action parameter was not found.
     */
    public function matchRequiredAction(array $required, array $params, string $set)
    {
        
        $swappedRequired = [];
        
        $notRequired     = $this->dbinfo->getAutoCompleted($set);
        
        foreach ($notRequired as $item) {
            $key = array_search($item, $required);
            if ($key === true) {
                unset($required[$key]);
            }
        }

        foreach ($required as $key => $value) {
            $swappedRequired[$value] = $required[$key];
        }

        if (empty(array_diff_key($swappedRequired, $params)) === false) {
            $error = $this->setUpError($swappedRequired, $params);

            throw new RequiredException($error);
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
