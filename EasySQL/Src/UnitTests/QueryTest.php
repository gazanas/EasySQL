<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Query as Query;
use EasySQL\Src\Sets as Sets;

final class QueryTest extends TestCase
{

    protected $db;
    protected static $database;

    protected $sets;
    protected $sql;

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

        $this->sets = new Sets\Sets($this->db);

    }


    public function tearDown() {
        $this->db->query("DROP TABLE `test_users`");
        $this->db = null;
    }

    public static function tearDownAfterClass() {

        self::$database->dropDatabase();

        self::$database = null;

    }

    public function testSetupValidGetQueryWithoutParameters() {         
        $getQuery = new Query\GetQuery();

        $get = $getQuery->setUpQuery($this->sets, 'test_users', array());
        
        $expected = 'SELECT * FROM test_users';

        $this->assertSame($get, $expected);
    }

    public function testSetupValidGetQueryIncludingValidParameters() {
        $getQuery = new Query\GetQuery();

        $get = $getQuery->setUpQuery($this->sets, 'test_users', array('id' => 1));

        $expected = 'SELECT * FROM test_users WHERE id = ?';

        $this->assertSame($get, $expected);
    }

    public function testSetupGetQueryWithTheReturnParameterPassed() {         
        $valueQuery = new Query\GetQuery();

        $value = $valueQuery->setUpQuery($this->sets, 'test_users', array('return' => 'username'));

        $expected = 'SELECT username FROM test_users';

        $this->assertSame($value, $expected);
    }

    public function testSetupUpdateQueryWithTheRightActionParametersPassed() {         
        $updateQuery = new Query\UpdateQuery();

        $update = $updateQuery->setUpQuery($this->sets, 'test_users', array('to_update' => 'username', 'updated' => 'test_update'));

        $expected = 'UPDATE test_users SET username = ?';

        $this->assertSame($update, $expected);
    }

    public function testSetupDeleteQuery() {
        $deleteQuery = new Query\DeleteQuery();

        $delete = $deleteQuery->setUpQuery($this->sets, 'test_users', array());

        $expected = 'DELETE FROM test_users';

        $this->assertSame($delete, $expected);
    }

    public function testSetupInsertQueryWhenTheRightParametersArePassedButDefaultValueColumnMissing() {         
        $parameters = array(
                    'username' => 'test_user',
                    'mail' => 'test_mail',
                    'password' => 'secret',
                    //is_active missing should get default value 0
                    'role' => 'user',
                    'created_at' => '2018-05-22'
                );

        $insertQuery = new Query\InsertQuery();
        
        $insert = $insertQuery->setUpQuery($this->sets, 'test_users', $parameters);
        
        $expected = 'INSERT INTO test_users(id, username, mail, password, role, created_at) VALUES(NULL,?,?,?,?,?)';

        $this->assertSame($insert, $expected);
    }

    public function testSetupInsertQueryWhenTheRightParametersArePassed() {         
        $parameters = array(
                    'username' => 'test_user',
                    'mail' => 'test_mail',
                    'password' => 'secret',
                    'is_active' => 0,
                    'role' => 'user',
                    'created_at' => '2018-05-22'
                );

        $insertQuery = new Query\InsertQuery();

        $insert = $insertQuery->setUpQuery($this->sets, 'test_users', $parameters);
        
        $expected = 'INSERT INTO test_users(id, username, mail, password, is_active, role, created_at) VALUES(NULL,?,?,?,?,?,?)';

        $this->assertSame($insert, $expected);
    }
}
