<?php

namespace EasySQL\Src\Query;

use EasySQL\Src\Data\DAO;
use EasySQL\Src\Parameters\Parameters;
use EasySQL\Src\Clause\ClauseInterface;

abstract class ClausableQuery
{
    
    private $dao;
    
    private $where;
    
    private $options;
    
    protected $parameters;
    
    protected $query;
    
    private $optionsClause;
    
    private $whereClause;
    
    protected $table;
    
    protected $params;
    
    public function __construct(DAO $dao, Parameters $parameters, ClauseInterface $where, ClauseInterface $options, string $table, string $join = null, string $onTable = null, string $onJoined = null)
    {
        $this->dao = $dao;
        $this->parameters = $parameters;
        $this->where = $where;
        $this->options = $options;
        $this->table = $table;
        $this->params = [];
        $this->optionsClause = '';
        $this->whereClause = '';
        
        if(isset($join, $onTable, $onJoined))
            $this->__init__($join, $onTable, $onJoined);
        else
            $this->__init__();
    }
    
    /**
     * Set up the where clause of the query.
     * 
     * @param array $params
     * 
     * @return ClausableQuery
     */
    public function where(array $params)
    {   
        try {
            $this->params = array_merge($this->params, $params);
        
            $this->whereClause = $this->where->prepareClause($params);
     
            $this->params = $this->parameters->prepareParameters($this->table, $this->params);
        
            return $this;
        } catch (\Exception $e) {
            print($e->getMessage());
            return;
        }
   }
    
    /**
     * Set up the options clause of the query
     * 
     * @param array $options
     * 
     * @return ClausableQuery
     */
    public function options(array $options)
    {
        try {
            
            $this->optionsClause = $this->options->prepareClause($options);
        
            return $this;
        } catch (\Exception $e) {
            print($e->getMessage());
            return;
        }
    }
    
    /**
     * Execute the SQL query.
     * 
     * @return array|string
     */
    public function execute()
    {
        try {
            $this->query .= $this->whereClause.$this->optionsClause;
            return $this->dao->executeQuery($this->query, array_values($this->params));
        } catch (\Exception $e) {
            print($e->getMessage());
            return;
        }
    }
}
