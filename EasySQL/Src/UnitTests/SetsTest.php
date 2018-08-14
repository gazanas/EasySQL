<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Data as Data;

final class SetsTest extends TestCase
{

    protected $db;
    protected static $database;

    public static function setUpBeforeClass() {
        self::$database = new Connection();

        self::$database->createDatabase();

    }    

    public function setUp() {

        $database = new Connection();

        $this->db = $database->getDB();

        $database = null;

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


    public function tearDown() {
        $this->db->query("DROP TABLE `test_users`");
        $this->db = null;
    }

    public static function tearDownAfterClass() {

        self::$database->dropDatabase();

        self::$database = null;

    }

    public function testGetTablesReturnsArrayContainingTableNames() {
    	$dbinfo = new Data\Sets($this->db);

    	$tables = $dbinfo->getTables('test');

    	$expected = array(
    		'test_users'
    	);

    	$this->assertSame($tables, $expected);
    }

    public function testGetColumnsReturnsArrayContainingTableColumnNames() {
    	$dbinfo = new Data\Sets($this->db);

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

    public function testGetColumnsThrowsExceptionWhenTableDoesNotExist() {
        $dbinfo = new Data\Sets($this->db);

        $this->expectException(\Exception::class);

        $columns = $dbinfo->getColumns('<invalid data set>');
    }

    public function testGetColumnInfoReturnsArrayContainingAllTheColumnsInfo() {
        $dbinfo = new Data\Sets($this->db);

        $columns_info = $dbinfo->getColumnsInfo('test_users');
        $expected = array (
            array (
                    'Field' => 'id',
                    'Type' => 'int(10) unsigned',
                    'Null' => 'NO',
                    'Key' => 'PRI',
                    'Default' => NULL,
                    'Extra' => 'auto_increment'
                ),

            array (
                    'Field' => 'username',
                    'Type' => 'varchar(15)',
                    'Null' => 'NO',
                    'Key' => 'UNI',
                    'Default' => NULL,
                    'Extra' => ''
                ),

            array (
                    'Field' => 'mail',
                    'Type' => 'varchar(320)',
                    'Null' => 'NO',
                    'Key' => 'UNI',
                    'Default' => NULL,
                    'Extra' => ''
                ),

            array (
                    'Field' => 'password',
                    'Type' => 'varchar(60)',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => NULL,
                    'Extra' => ''
                ),

            array (
                    'Field' => 'is_active',
                    'Type' => 'tinyint(1)',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => '0',
                    'Extra' => ''
                ),

            array (
                    'Field' => 'role',
                    'Type' => 'varchar(10)',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => 'user',
                    'Extra' => ''
                ),

            array (
                    'Field' => 'created_at',
                    'Type' => 'timestamp',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => NULL,
                    'Extra' => ''
                ),

            array (
                    'Field' => 'updated_at',
                    'Type' => 'timestamp',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => 'CURRENT_TIMESTAMP',
                    'Extra' => ''
                )
            );
        $this->assertSame($columns_info, $expected);
    }

    public function testGetColumnInfoThrowsExceptionWhenTableDoesNotExist() {
        $dbinfo = new Data\Sets($this->db);

        $this->expectException(\Exception::class);

        $columns_info = $dbinfo->getColumnsInfo('<invalid data set>');
    }

    public function testGetRequiredColumnsThrowsExceptionlWhenTableDoesNotExist() {
        $dbinfo = new Data\Sets($this->db);

        $this->expectException(\Exception::class);

        $columns = $dbinfo->getRequiredColumns('<invalide data set>');
    }

    public function testGetAutoCompletedColumnsReturnsArrayContainingTheColumnsNamesThatHaveAutoCompletedValues() {
    	$dbinfo = new Data\Sets($this->db);

    	$autos = $dbinfo->getAutoCompleted('test_users');

    	$expected = array(
            array(
                'name' => 'is_active',
                'type' => '0'
            ),
            array(
                'name' => 'role',
                'type' => 'user'
            ),
            array(
                'name' => 'updated_at',
                'type' => 'CURRENT_TIMESTAMP'
            ),
    		array(
    			'name' => 'id',
    			'type' => 'auto_increment'
    		)
    	);

    	$this->assertSame($autos, $expected);
    }
}