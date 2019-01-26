<?php

namespace EasySQL\Src\Clause;

use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Clause\Exceptions\InvalidOptionException;

class OptionsClause implements ClauseInterface
{

    private $sets;
    
    public function __construct(Sets $sets)
    {
        $this->sets = $sets;
    }

    /**
     * Setup the options of the query
     *
     * @param array  $params The parameters array passed by the user.
     *
     * @return string $options   The query including the options e.g. (LIMIT, ORDER BY).
     *
     * @throws \Exception       Option does not exist in options set.
     */
    public function prepareClause(array $params)
    {
        $options = '';
        $this->checkOptions($params);
            
        foreach ($params as $option => $value) {
            $option = preg_replace('/(order|ORDER)/', 'order by', $option);
            $options .= ' '.$option.' '.$value;
        }

        return $options;
    }

    /**
     * Checks whether the option exists in the options set
     *
     * @param array $params The parameters array passed by the user.
     *
     * @throws \Exception
     */
    private function checkOptions(array $params)
    {
        foreach (array_keys($params) as $option) {
            if(in_array($option, $this->sets->getOptionSet(), true) === false) {
                throw new InvalidOptionException('Option '.$option.' is not correct');
            }
        }
    
        $this->checkOrder($params);
        $this->checkLimit($params);
    }

    /**
     * Checks if order option value is a string
     *
     * @param array $params The option parameters array.
     *
     * @throws \Exception
     */
    private function checkOrder(array $params)
    {
        if(isset($params['order'])) {
            if(!is_string($params['order'])) {
                throw new InvalidOptionException('Cannot order by '.gettype($params['order']));
            }
        }
    }

    /**
     * Checks if limit option value is an integer
     *
     * @param array $params The option parameters array.
     *
     * @throws \Exception
     */
    private function checkLimit(array $params)
    {
        if(isset($params['limit'])) {
            if(!is_integer($params['limit'])) {
                throw new InvalidOptionException('Cannot limit by '.gettype($params['limit']));
            }
        }
    }
}
