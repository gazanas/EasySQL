<?php

namespace EasySQL\Src\Clause;

interface ClauseInterface {
	
	public function prepareClause($params);
}