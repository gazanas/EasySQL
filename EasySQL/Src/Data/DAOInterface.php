<?php

namespace EasySQL\Src\Data;

interface DAOInterface {
	
    public function get();

    public function insert();

    public function update();

    public function delete();
}
