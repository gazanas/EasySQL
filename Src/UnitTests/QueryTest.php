<?php

namespace Src\UnitTests;

use PHPUnit\Framework\TestCase;

use Src\Data\SQL as SQL;
use Src\Data as Data;

final class QueryTest extends TestCase
{

    protected $db;

    protected $config;

    protected $sql;


    public function setUp()
    {
        ob_start(); 

        $configuration = new Data\Configuration();

        $this->config = $configuration->getDatabaseConfig();

        $this->config[4] = 'test';

        $this->db = new \PDO($this->config[1].':host='.$this->config[2].';', $this->config[3], $this->config[5]);

        $this->db->query('CREATE DATABASE test');

        $this->db = null;

        $this->db = new \PDO(
            $this->config[1].':host='.$this->config[2].';dbname='
            .$this->config[4],
            $this->config[3],
            $this->config[5]
        );

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

        $this->db = null;

    }//end setUp()


    public function tearDown()
    {
        $this->db = new \PDO($this->config[1].':host='.$this->config[2].';', $this->config[3], $this->config[5]);

        $this->db->query('DROP DATABASE test');

        $this->db = null;

        ob_end_clean();

    }//end tearDown()

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

    }//end testReturnTrueWhenActionIsInTheActionSet()

    public function testReturnNullIfTheOptionPassedIsNotInTheOptionsSet() {
        $query = new Data\Query();

        $options = $query->queryOptions('SELECT * FROM test_users', array('id' => 1, 'options' => array('INVALID OPTION' => 1)));
    
        $this->assertNull($options);
    }

    public function testSetupInsertQueryFromTheParametersPassed() {
        $query = new Data\Query();
        
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
                    'username',
                    'mail',
                    'password',
                    'is_active',
                    'role',
                    'created_at'
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

        $insert = $query->setUpInsertQuery($allColumns, $notAutos, $autos);

        $expected = 'NULL,?,?,?,?,?,?,NOW())';

        $this->assertSame($insert, $expected);
    }

}//end class
