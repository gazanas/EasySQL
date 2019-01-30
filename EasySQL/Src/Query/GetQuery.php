<?php

namespace EasySQL\Src\Query;

class GetQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     */
    protected function init()
    {
        $this->query = 'SELECT * FROM '.$this->table;
    }
    
    public function join(string $table, string $onTable, string $onJoined)
    {
        $this->sets->getColumnsInfo($table);
        
        $join = $this->joinQuery($table, $onTable, $onJoined);
        
        $this->query = 'SELECT * FROM '.$this->table.' JOIN '.$table.' ON '.$join['left'].'='.$join['right'];
        
        return $this;
    }
    
    /**
     * Checks if the joined tables and parameters exist and returns the join parameters query
     *
     * @param string $table
     * @param string $onTable
     * @param string $onJoined
     *
     * @return string[]
     */
    private function joinQuery(string $table, string $onTable, string $onJoined)
    {
        $leftMatches = $rightMatches = [];
        
        if (preg_match('/([A-Za-z0-9_]+)\.([A-Za-z0-9_]+)/', $onTable, $leftMatches)) {
            $this->sets->getColumnsInfo($leftMatches[1]);
            $this->parameters->checkFieldExists($leftMatches[2], $leftMatches[1]);
            $left = $onTable;
        } else {
            $left = $this->table.'.'.$onTable;
        }
        
        if (preg_match('/([A-Za-z0-9_]+)\.([A-Za-z0-9_]+)/', $onJoined, $rightMatches)) {
            $this->sets->getColumnsInfo($rightMatches[1]);
            $this->parameters->checkFieldExists($rightMatches[2], $rightMatches[1]);
            $right = $onJoined;
        } else {
            $right = $table.'.'.$onJoined;
        }
        
        return ['left' => $left, 'right' => $right];
    }
    
    /**
     * Extracts the return values clause for the get query.
     *
     * @param array|string $returnFields The fields to return passed by the user.
     *
     * @return ClausableQuery
     */
    public function return(...$returnFields)
    {
        $matches = [];
        $fields = '';
        
        foreach ($returnFields as $field) {
            $table = $this->table;
            $check = $field;
                
            /**
             * If returned rows must be distinct
             */
            if (preg_match('/(DISTINCT|distinct) ([A-Za-z0-9_\.\(]+)/', $check, $matches)) {
                $check = $matches[2];
            }

            /**
             * Return the number of the rows
             */
            if (preg_match('/(COUNT|count)\(([A-Za-z0-9_\.]+)/', $check, $matches)) {
                $check = $matches[2];
            }
                    
            /**
             * Is the table referenced
             */
            if (preg_match('/([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)/', $check, $matches)) {
                $check = $matches[2];
                $table = $matches[1];
            }

            $this->parameters->checkFieldExists($check, $table);
                        
            $fields .= $field.', ';
        }
            
        $fields = preg_replace('/, $/', '', $fields);
            
        $this->query = preg_replace('/^SELECT \* FROM/', 'SELECT '.$fields.' FROM', $this->query);
            
        return $this;
    }
    
    /**
     * Set up the group clause of the query
     *
     * @param string $field
     */
    public function group(string $field)
    {
        $this->groupClause = $this->group->prepareClause([$field]);
        return $this;
    }
}
