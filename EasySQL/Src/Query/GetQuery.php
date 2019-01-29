<?php

namespace EasySQL\Src\Query;

class GetQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     */
    protected function __init__()
    {
        $this->query = 'SELECT * FROM '.$this->table;        
    }
    
    public function join(string $table, string $onTable, string $onJoined)
    {
        $left = preg_match('/[A-Za-z0-9_]\.[A-Za-z0-9_]/', $onTable) ? $onTable : $this->table.'.'.$onTable;
        $right = preg_match('/[A-Za-z0-9_]\.[A-Za-z0-9_]/', $onJoined) ? $onJoined : $table.'.'.$onJoined;
        $this->query = 'SELECT * FROM '.$this->table.' JOIN '.$table.' ON '.$left.'='.$right;  
        
        return $this;
    }
    
    /**
     * Extracts the return values clause for the get query.
     *
     * @param array|string $returnFields    The fields to return passed by the user.
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
            if (preg_match('/(DISTINCT|distinct) ([A-Za-z0-9_\.\(]+)/', $check, $matches))
                $check = $matches[2];

            /**
             * Return the number of the rows
             */
            if(preg_match('/(COUNT|count)\(([A-Za-z0-9_\.]+)/', $check, $matches))
                $check = $matches[2];
                    
            /**
             * Is the table referenced
             */
            if(preg_match('/([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)/', $check, $matches)) {
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
