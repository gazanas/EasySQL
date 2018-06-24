<?php

namespace EasySQL\Src\Data;

class QueryFactory {
	
	public function getQueryType(string $queryType = null) {
		switch($queryType) {
			case 'insert':
				$query =  new InsertQuery();
				break;
			default:
				$query = new Query();
		}
		
		return $query;
	}
}