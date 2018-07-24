<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Data\SQL as SQL;
use EasySQL\Src\Data as Data;

final class QueryTest extends TestCase
{

    protected $db;

    protected $config;

    protected $sql;


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

    public function testSetUpWhereClauseOfTheQueryFromTheParametersArrayPassed()
    {
        $query = new Data\Query();

        $where = $query->simpleWhereClause(array('id' => 1));

        $expected = ' WHERE id = ?';

        $this->assertSame($where, $expected);

    }

    public function testReturnNullWhenParametersArrayIsEmpty() {
        $query = new Data\Query();

        $where = $query->simpleWhereClause(array());

        $this->assertNull($where);
    }

    public function testThrowExceptionWhenParametersPassedIsNotArray() {
        $query = new Data\Query();

        $this->expectException(\TypeError::class);

        $where = $query->simpleWhereClause('test');
    }

    public function testSetUpQueryOptionsFromTheParametersArrayPassed()
    {
        $query = new Data\Query();
        
        $options = $query->queryOptions('SELECT * FROM test_users', array('id' => 1, 'options' => array('LIMIT' => 1)));

        $expected = 'SELECT * FROM test_users LIMIT 1';

        $this->assertSame($options, $expected);

    }

    public function testReturnNullIfTheOptionPassedIsNotInTheOptionsSet() {
        $query = new Data\Query();

        $this->expectException(Data\OptionsException::class);

        $options = $query->queryOptions('SELECT * FROM test_users', array('id' => 1, 'options' => array('INVALID OPTION' => 1)));
    }

    public function testSetupInsertQueryFromTheParametersPassed() {
        $query = new Data\InsertQuery();
        
        $allColumns = array(
                        'id',
                        'username',
                        'mail',
                        'password',
                        'is_active',
                        'role',
                        'created_at',
                        'updated_at'
                    );

        $notAutos = array(
                    'username' => 'test_user',
                    'mail' => 'test_mail',
                    'password' => 'secret',
                    'is_active' => 0,
                    'role' => 'user',
                    'created_at' => '2018-05-22'
                );

        $autos = array (
                    array(
                        'name' => 'id',
                        'type' => 'auto_increment'
                    ),
                    array(
                        'name' => 'updated_at',
                        'type' => 'current_timestamp'
                    )
                );

        $nullable = array(
                    'created_at' => 'created_at'
                );

        $insert = $query->setUpInsertQuery($allColumns, $notAutos, $autos, $nullable);

        $expected = 'NULL,?,?,?,?,?,?,NOW())';

        $this->assertSame($insert, $expected);
    }

}
