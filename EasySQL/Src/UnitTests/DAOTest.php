<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Sets as Sets;
use EasySQL\Src\Data as Data;
use EasySQL\Src\Parameters as Parameters;
use EasySQL\Src\Query as Query;

class DAOTest extends TestCase
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
        $this->sql = new Data\SQL($this->db);

    }


    public function tearDown() {
        $this->db->query("DROP TABLE `test_users`");
        $this->db = null;
    }
    public static function tearDownAfterClass() {

        self::$database->dropDatabase();

        self::$database = null;

    }

    public function testDAOGetCallReturnsAnArrayOfAllTableRows()
    {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\GetParameters($this->sets), new Query\GetQuery($this->sets), 'test_users', array());
        $data = $dao->get();

        $expected = array(
            [
                'id' => 1,
                'username' => 'root',
                'mail' => 'root@mysite.com',
                'password' => 'secret',
                'is_active' => 1,
                'role' => 'admin',
                'created_at' => '2018-05-21 21:00:00',
                'updated_at' => '2018-05-22 15:55:42'
            ],
            [
                'id' => 2,
                'username' => 'dani',
                'mail' => 'dani@example.com',
                'password' => 'terces',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-21 21:00:00',
                'updated_at' => '2018-05-22 15:56:03'
            ]
        );

        $this->assertEquals($data, $expected);

    }

    public function testDAOGetCallReturnsAnArrayOfCertainTableRows()
    {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\GetParameters($this->sets), new Query\GetQuery($this->sets), 'test_users', array('id' => 1));

        $data = $dao->get();

        $expected = array(
                            array(
                                'id' => 1,
                                'username' => 'root',
                                'mail' => 'root@mysite.com',
                                'password' => 'secret',
                                'is_active' => 1,
                                'role' => 'admin',
                                'created_at' => '2018-05-21 21:00:00', 
                                'updated_at' => '2018-05-22 15:55:42'
                            )
                        );

        $this->assertEquals($data, $expected);
    }

    public function testDAOGetCallReturnEmptyArrayWhenRowDoesNotExist() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\GetParameters($this->sets), new Query\GetQuery($this->sets), 'test_users',  array('id' => 999));

        $data = $dao->get();

        $expected = array();

        $this->assertEquals($data, $expected);
    }

    public function testDAOGetCallThrowsExceptionWhenInvalidParametersPassed() {
        $this->expectException(\Exception::class);

        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\GetParameters($this->sets), new Query\GetQuery($this->sets), 'test_users', array('<invalid parameter>' => 1));
    }

    public function testDAOGetCallReturnsAnArrayOfOneTableColumn() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\GetParameters($this->sets), new Query\GetQuery($this->sets), 'test_users', array('return' => 'username', 'id' => 1));
        $data     = $dao->get();

        $expected = array(
                            array(
                                'username' => 'root'
                            ));

        $this->assertEquals($expected, $data);

    }

    public function testDAOVGetCallReturnsEmptyArrayWhenRowDoesNotExist() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\GetParameters($this->sets), new Query\GetQuery($this->sets), 'test_users', array('return' => 'username', 'id' => 999));

        $data = $dao->get();

        $expected = array();

        $this->assertEquals($data, $expected);
    }

    public function testDAOUpdateCallUpdatesOneTableRow() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\UpdateParameters($this->sets), new Query\UpdateQuery($this->sets), 'test_users', array('to_update' => 'username', 'updated' => 'updated_root', 'id' => 1));
        
        $data = $dao->update();

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);

    }

    public function testDAOUpdateCallUpdatesAllTableRows() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\UpdateParameters($this->sets), new Query\UpdateQuery($this->sets), 'test_users', array('to_update' => 'is_active', 'updated' => 1));

        $data = $dao->update();

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);
    }

    public function testDAOUpdateCallThrowsExceptionWhenUpdatedValueOnAUniqueColumn() {        
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\UpdateParameters($this->sets), new Query\UpdateQuery($this->sets), 'test_users', array('to_update' => 'username', 'updated' => 'root'));
        
        $this->expectException(\Exception::class);

        $dao->update();

    }


    public function testDAOUpdateCallThrowsExceptionWhenUpdateActionParametersAreMissing() {        
        $this->expectException(\Exception::class);
        
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\UpdateParameters($this->sets), new Query\UpdateQuery($this->sets), 'test_users', array('to_update' => 'username'));

    }


    public function testDAODeleteCallDeletesOneTableRow() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\DeleteParameters($this->sets), new Query\DeleteQuery($this->sets), 'test_users', array('id' => 1));

        $data = $dao->delete();
        
        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);

    }


    public function testDeleteDAOCallDeletesAllTableRows() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\DeleteParameters($this->sets), new Query\DeleteQuery($this->sets), 'test_users', array());

        $data = $dao->delete();

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);

    }

    public function testDAODeleteCallThrowsExceptionWhenRowDoesNotExist() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\DeleteParameters($this->sets), new Query\DeleteQuery($this->sets), 'test_users', array('id' => 999));

        $this->expectException(\Exception::class);

        $data = $dao->delete();
    }


    public function testDAOInsertCallInsertsANewRow() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\InsertParameters($this->sets), new Query\InsertQuery($this->sets), 'test_users', array('username' => 'test_user',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'));

        $data = $dao->insert();


        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);

    }

    public function testDAOInsertCallThrowsExceptionWhenRequiredColumnParameterIsMissing() {
        $this->expectException(\Exception::class);

        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\InsertParameters($this->sets), new Query\InsertQuery($this->sets), 'test_users', array(
                //missing username
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
            ));

    }

    public function testDAOInsertCallThrowsExceptionWhenInsertingDuplicateValueOnAUniqueColumn() {
        $dao = new Data\DAO($this->sql, $this->sets, new Parameters\InsertParameters($this->sets), new Query\InsertQuery($this->sets), 'test_users', array(
                'username' => 'root',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
            ));

        $this->expectException(\Exception::class);

        $data = $dao->insert();

    }
}
