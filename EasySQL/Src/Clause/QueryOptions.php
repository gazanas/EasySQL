<?php

namespace EasySQL\Src\Clause;

use EasySQL\Src\Sets as Sets;

class QueryOptions {

	protected $sets;
	
	public function __construct(Sets\Sets $sets) {
        $this->sets = $sets;
	}

	/**
     * Setup the options of the query
     *
     * @param string $query     The query string to be parsed for available options.
     * @param array  $params    The parameters array passed by the user.
     *
     * @return string $query    The query including the options e.g. (LIMIT, ORDER BY).
     *
     * @throws \Exception       Option does not exist in options set.
     */
    public function queryOptions(string $query, $params) {
        if (isset($params['options']) === true && is_array($params['options']) === true) {
            $this->checkOptions($params['options']);
            foreach ($params['options'] as $option => $value) {
                $option = preg_replace('/(order|ORDER)/', 'order by', $option);
                $query .= ' '.$option.' '.$value;
            }
        }

        return $query;
    }

    /**
     * Checks whether the option exists in the options set
     *
     * @param array $params     The parameters array passed by the user.
     *
     * @throws \Exception
     */
    private function checkOptions(array $params) {
        foreach ($params as $option => $value)
            if(in_array($option, $this->sets->getOptionSet(), true) === false)
                throw new \Exception('Option '.$option.' is not correct');
    
        $this->checkOrder($params);
        $this->checkLimit($params);
    }

    /**
     * Checks if order option value is a string
     *
     * @param array $params     The option parameters array.
     *
     * @throws \Exception
     */
    private function checkOrder(array $params) {
        if(isset($params['order']))
            if(!is_string($params['order']))
                throw new \Exception('Cannot order by '.gettype($params['order']));
    }

    /**
     * Checks if limit option value is an integer
     *
     * @param array $params     The option parameters array.
     *
     * @throws \Exception
     */
    private function checkLimit(array $params) {
        if(isset($params['limit']))
            if(!is_integer($params['limit']))
                throw new \Exception('Cannot limit by '.gettype($params['limit']));
    }
}
