<?php

namespace EasySQL\Src\Query;

interface ClauseInterface {
	
	public function prepareClause($params);
}