<<<<<<< HEAD
<?php

namespace EasySQL\Src\Parameters;

use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Parameters\Exceptions\FieldNotFoundException;

abstract class Parameters
{
    
    protected $sets;
    
    /**
     * Intializes the parameters object.
     *
     * @param Sets $sets The sets object.
     */
    public function __construct(Sets $sets)
    {
        $this->sets = $sets;
    }
    
    /**
     * Checks if the field exists.
     *
     * @param mixed  $field The field name.
     * @param string $table The table name.
     */
    public function checkFieldExists(string $field, string $table)
    {
        if(!in_array($field, $this->sets->getColumns($table), true)) {
            throw new FieldNotFoundException($field);
        }
    }
}
=======
<?php

namespace EasySQL\Src\Parameters;

abstract class Parameters {

	 /**
     * Prepares the parametres to be passed pdo's bindParam
     *
     * @param string $table         The name of the table.
     * @param array  $params        The parameters array passed by the user.
     *
     * @return array $bind_params   The prepared parameters for execution.
     * 
     * @throws \Exception           Wrong type of parameter passed.
     */
    public function prepareParameters(string $table, $params) {
    
        if(is_array($params) === false)
            throw new \Exception('Wrong parameter type passed.');
        
        $params = array_diff_key($params, array('options' => 1, 'condition' => 1));

        $bindParams = $this->extractParameters($params, $table);

        return $bindParams;
    }

    /**
    * Checks if the field exists.
    *
    * @param mixed $field       The field name.
    * @param string $table      The table name.
    */
    protected function checkFieldExists($field, $table) {
        if(!in_array($field, $this->sets->getColumns($table), true))
            throw new \Exception('Field '.$field.' was not found.');
    }

    protected abstract function extractParameters(array $params, string $table = null);
}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
