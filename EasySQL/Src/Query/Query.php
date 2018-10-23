<?php

namespace EasySQL\Src\Query;

use EasySQL\Src\Sets as Sets;

interface Query {
    
    public function initializeQuery(Sets\Sets $sets, string $table, array $params);
	
	public function setUpQuery(Sets\Sets $sets, string $table, array $params);
}