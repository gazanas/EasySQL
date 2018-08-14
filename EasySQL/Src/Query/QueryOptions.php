<?php

namespace EasySQL\Src\Query;

class QueryOptions {

	protected $sets;
	
	public function __construct($sets) {
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
    public function queryOptions(string $query, $params)
    {
        if (isset($params['options']) === true && is_array($params['options']) === true) {
            if ($this->checkOptions($params['options']) === true) {
                foreach ($params['options'] as $option => $value) {
                    $option = preg_replace('/order/', 'order by', $option);
                    $query .= ' '.$option.' '.$value;
                }
            } else {
                throw new \Exception('Option is not correct');
            }
        }

        return $query;
    }

    /**
     * Checks whether the option exists in the options set
     *
     * @param array $params     The parameters array passed by the user.
     *
     * @return boolean
     */
    private function checkOptions(array $params)
    {
        $flag = false;
        foreach ($params as $option => $value) {
            if (in_array($option, $this->sets->getOptionSet()) === true) {
                $flag = true;
            } else {
                $flag = false;
            }
        }

        return $flag;
    }
}