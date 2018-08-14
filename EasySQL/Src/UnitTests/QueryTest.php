<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

<<<<<<< HEAD
use EasySQL\Src\Query as Query;
=======
use EasySQL\Src\Data\SQL as SQL;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
use EasySQL\Src\Data as Data;

final class QueryTest extends TestCase
{

    protected $db;
<<<<<<< HEAD
    protected static $database;

    public static function setUpBeforeClass() {
        self::$database = new Connection();

        self::$database->createDatabase();

    }    

    public function setUp() {

        $database = new Connection();

        $this->db = $database->getDB();

        $database = null;
=======

    protected $config;

    protected $sql;


    public function setUp()
    {
        ob_start(); 

        $database = new Connection();

        $database->createDatabase();

        $this->db = $database->getDB();
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

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


<<<<<<< HEAD
    public function tearDown() {
        $this->db->query("DROP TABLE `test_users`");
        $this->db = null;
    }

    public static function tearDownAfterClass() {

        self::$database->dropDatabase();

        self::$database = null;

    }

    public function testSetupInvalidGenericQueryUsingInvalidParametersType() {         
        $sets = new Data\Sets($this->db);

        $query = new Query\Query();
        
        $this->expectException(\Exception::class);

        $query->setUpQuery($sets, 'invalid action', 'test_users', 'bogus parameters');
    
    }

    public function testSetupInvalidGenericQueryUsingInvalidAction() {
        $sets = new Data\Sets($this->db);

        $query = new Query\Query();

        $this->expectException(\Exception::class);

        $query->setUpQuery($sets, 'invalid action', 'test_users', array());
    }

    public function testSetupValidGetQueryWithoutParameters() {         
        $sets = new Data\Sets($this->db);
        
        $getQuery = new Query\Query();
        $get = $getQuery->setUpQuery($sets, 'get', 'test_users', array());
        
        $expected = 'SELECT * FROM test_users';

        $this->assertSame($get, $expected);
    }

    public function testSetupValidGetQueryIncludingValidParameters() {
        $sets = new Data\Sets($this->db);
        
        $getQuery = new Query\Query();
        $get = $getQuery->setUpQuery($sets, 'get', 'test_users', array('id' => 1));

        $expected = 'SELECT * FROM test_users WHERE id = ?';

        $this->assertSame($get, $expected);
    }

    public function testSetupValueQueryWithTheRightActionParameterPassed() {         
        $sets = new Data\Sets($this->db);
        
        $valueQuery = new Query\Query();
        $value = $valueQuery->setUpQuery($sets, 'value', 'test_users', array('return' => 'username'));

        $expected = 'SELECT username FROM test_users';

        $this->assertSame($value, $expected);
    }

    public function testSetupUpdateQueryWithTheRightActionParametersPassed() {         
        $sets = new Data\Sets($this->db);
        
        $updateQuery = new Query\Query();
        $update = $updateQuery->setUpQuery($sets, 'update', 'test_users', array('to_update' => 'username', 'updated' => 'test_update'));

        $expected = 'UPDATE test_users SET username = ?';

        $this->assertSame($update, $expected);
    }

    public function testSetupDeleteQuery() {
        $sets = new Data\Sets($this->db);

        $deleteQuery = new Query\Query();
        $delete = $deleteQuery->setUpQuery($sets, 'delete', 'test_users', array());

        $expected = 'DELETE FROM test_users';

        $this->assertSame($delete, $expected);
    }

    public function testSetupInsertQueryWhenTheRightParametersArePassedButDefaultValueColumnMissing() {         
        $parameters = array(
                    'username' => 'test_user',
                    'mail' => 'test_mail',
                    'password' => 'secret',
                    //is_active missing should get default value 0
=======
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
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
                    'role' => 'user',
                    'created_at' => '2018-05-22'
                );

<<<<<<< HEAD
        $sets = new Data\Sets($this->db);

        $insertQuery = new Query\Query();
        $insert = $insertQuery->setUpQuery($sets, 'insert', 'test_users', $parameters);
        
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

        $sets = new Data\Sets($this->db);

        $insertQuery = new Query\Query();
        $insert = $insertQuery->setUpQuery($sets, 'insert', 'test_users', $parameters);
        
        $expected = 'INSERT INTO test_users(id, username, mail, password, is_active, role, created_at) VALUES(NULL,?,?,?,?,?,?)';

        $this->assertSame($insert, $expected);
    }
=======
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

>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
}
