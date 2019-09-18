<?php

namespace EasySQL\Query;

use EasySQL\Query\Syntax\SqlSyntax;

class SqliteSyntax extends SqlSyntax
{
	/**
	 * Get the query fetching tables of the database
	 * 
	 * @return string
	 */
    public function tables()
    {
    	return ".tables";
    }

    /**
     * Get the query fetching the columns of a table
     * 
     * @return string
     */
    public function columns(string $table)
    {
 	   	return "PRAGMA table_info({$table})";
    }
}
