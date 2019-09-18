<?php

namespace EasySQL\Query;

use EasySQL\Query\Syntax\SqlSyntax;

class MysqlSyntax extends SqlSyntax
{
	/**
	 * Get the query fetching tables of the database
	 * 
	 * @return string
	 */
    public function tables()
    {
    	return "SHOW TABLES";
    }

    /**
     * Get the query fetching the columns of a table
     * 
     * @return string
     */
    public function columns(string $table)
    {
 	   	return "SHOW COLUMNS FROM {$table}";
    }
}
