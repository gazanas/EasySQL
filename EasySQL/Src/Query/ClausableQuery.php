<?php

namespace EasySQL\Src\Query;

use EasySQL\Src\Data\DAO;
use EasySQL\Src\Parameters\Parameters;
use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Clause\ClauseInterface;

abstract class ClausableQuery
{
    
    private $dao;
    
    private $where;
    
    private $options;
    
    protected $group;
    
    protected $parameters;
    
    protected $query;
    
    private $optionsClause;
    
    private $whereClause;
    
    protected $groupClause;
    
    protected $table;
    
    protected $params;
    
    protected $sets;
    
    public function __construct(
        DAO $dao,
        Parameters $parameters,
        ClauseInterface $where,
        ClauseInterface $options,
        string $table,
        Sets $sets,
        ClauseInterface $group = null
    ) {
        $this->dao = $dao;
        $this->parameters = $parameters;
        $this->where = $where;
        $this->options = $options;
        $this->group = $group;
        $this->table = $table;
        $this->params = [];
        $this->optionsClause = '';
        $this->whereClause = '';
        $this->groupClause = '';
        $this->sets = $sets;
        
        $this->sets->getColumnsInfo($table);
        
        $this->init();
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
        $this->params = array_merge($this->params, $params);
        
        $this->whereClause = $this->where->prepareClause($params);
     
        $this->params = $this->parameters->prepareParameters($this->table, $this->params);
        
        return $this;
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
        $this->optionsClause = $this->options->prepareClause($options);
        
        return $this;
    }
    
    /**
     * Execute the SQL query.
     *
     * @return array|string
     */
    public function execute()
    {
        $this->query .= $this->whereClause.$this->groupClause.$this->optionsClause;
        return $this->dao->executeQuery($this->query, array_values($this->params));
    }
}
