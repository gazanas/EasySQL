<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Data\DatabaseInfo as DatabaseInfo;

use EasySQL\Src\Data as Data;
use EasySQL\Src\API as API;

final class DatabaseInfoTest extends TestCase
{

    protected $db;

    protected $config;


    public function setUp()
    {
        ob_start(); 

        $database = new Connection();

        $database->createDatabase();

        $this->db = $database->getDB();

        $this->db->query(
            "CREATE TABLE `test_users` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `username` varchar(15) NOT NULL,
                          `mail` varchar(320) NOT NULL,
                          `password` varchar(60) NOT NULL,
                          `is_active` tinyint(1) NOT NULL DEFAULT '0',
                          `role` varchar(10) NOT NULL DEFAULT 'user',
                          `created_at` timestamp NULL DEFAULT NULL,
                          `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `username` (`username`),
                          UNIQUE KEY `mail` (`mail`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;"
        );

        $this->db->query(
            "INSERT INTO `test_users` VALUES (1,'root','root@mysite.com','secret',1,'admin',
            '2018-05-21 21:00:00','2018-05-22 15:55:42'),(2,'dani','dani@example.com','terces',0,'user',
            '2018-05-21 21:00:00','2018-05-22 15:56:03');
            "
        );

    }


    public function tearDown()
    {
        $this->db = null;

        $database = new Connection();

        $database->dropDatabase();
        
        ob_end_clean();

    }

    public function testGetTables() {
    	$dbinfo = new API\DatabaseDAO($this->db);

    	$tables = $dbinfo->getTables($this->config[4]);

    	$expected = array(
    		'test_users'
    	);

    	$this->assertSame($tables, $expected);
    }

    public function testGetColumns() {
    	$dbinfo = new API\DatabaseDAO($this->db);

    	$columns = $dbinfo->getColumns('test_users');

    	$expected = array(
    		'id',
    		'username',
    		'mail',
    		'password',
    		'is_active',
    		'role',
    		'created_at',
    		'updated_at'
    	);

    	$this->assertSame($columns, $expected);
    }

    public function testGetColumnsThatCanHaveNullAsValue() {
    	$dbinfo = new API\DatabaseDAO($this->db);

    	$columns = $dbinfo->getNullableColumns('test_users');

    	$expected = array(
    		'created_at' => 'created_at'
    	);

    	$this->assertSame($expected, $columns);
    }

    public function testReturnNullWhenTableNamePassedDoesNotExist() {
        $dbinfo = new API\DatabaseDAO($this->db);

        $columns = $dbinfo->getNullableColumns('<invalide data set>');

        $this->assertFalse($columns);
    }

    public function testGetNotNullableColumns() {
    	$dbinfo = new API\DatabaseDAO($this->db);

    	$required = $dbinfo->getRequiredColumns('test_users');

    	$expected = array(
    		'username' => 'username',
    		'mail' => 'mail',
    		'password' => 'password',
    		'is_active' => 'is_active',
    		'role' => 'role'
    	);

    	$this->assertSame($required, $expected);
    }

    public function testGetColumnsWhichHaveAutoCompletedValues() {
    	$dbinfo = new API\DatabaseDAO($this->db);

    	$autos = $dbinfo->getAutoCompleted('test_users');

    	$expected = array(
    		array(
    			'name' => 'id',
    			'type' => 'auto_increment'
    		),
    		array(
    			'name' => 'updated_at',
    			'type' => 'current_timestamp'
    		)
    	);

    	$this->assertSame($autos, $expected);
    }

    public function testReturnNullIfTableNamePassedDoesNotExist() {
        $dbinfo = new API\DatabaseDAO($this->db);

        $autos = $dbinfo->getAutoCompleted('<invalid data set>');

        $this->assertFalse($autos);
    }
}