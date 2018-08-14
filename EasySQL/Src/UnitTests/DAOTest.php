<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

<<<<<<< HEAD
use EasySQL\Src\Data as Data;
=======
use EasySQL\Src\Collection\Collection as Collection;
use EasySQL\Src\Data as Data;
use EasySQL\Src\API as API;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

class DAOTest extends TestCase
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

    public function testDAOGetCallReturnsAnArrayOfAllTableRows()
    {
        $dao  = new Data\DAO(new Data\Sets($this->db), 'test_users', 'get', array(), $this->db);
        $data = $dao->get();

        $expected = array(
=======
    public function tearDown()
    {
        $this->db = null;

        $database = new Connection();

        $database->dropDatabase();
        
        ob_end_clean();

    }


    public function testCorrectDAOCallReturnsACollectionObject()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->get();

        $this->assertInstanceOf(Collection::class, $data);

    }

    public function testGetCollectionOfAllTableRows()
    {
        $dao  = new API\DAOs\Test_Users('test_users', $this->db);
        $data = $dao->get();

        $expected = new Collection(
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
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

<<<<<<< HEAD
    public function testDAOGetCallReturnsAnArrayOfCertainTableRows()
    {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'get', array('id' => 1), $this->db);

        $data = $dao->get();

        $expected = array(
=======
    public function testGetCollectionOfCertainTableRows()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->get(array('id' => 1));

        $expected = new Collection(
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
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

<<<<<<< HEAD
        $this->assertEquals($data, $expected);
    }

    public function testDAOGetCallReturnEmptyArrayWhenRowDoesNotExist() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'get', array('id' => 999), $this->db);

        $data = $dao->get();

        $expected = array();

        $this->assertEquals($data, $expected);
    }

    public function testDAOGetCallThrowsExceptionWhenInvalidParametersPassed() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'get', array('<invalid parameter>' => 1), $this->db);

        $this->expectException(\PDOException::class);

        $data = $dao->get();
    }

    public function testDAOGetCallThrowsExceptionWhenInvalidParameterTypePassed() {
        $this->expectException(\Exception::class);

        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'get', '<invalid parameter>', $this->db);
    }

    public function testDAOValueCallReturnsAnArrayOfOneTableColumn() {
        $dao      = new Data\DAO(new Data\Sets($this->db), 'test_users', 'value', array('return' => 'username', 'id' => 1), $this->db);
        $data     = $dao->value();

        $expected = array(
=======
        $this->assertEquals($expected, $data);
    }

    public function testGetCollectionReturnsFalseWhenWrongParameterPassed() {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->get(array('<invalid parameter>' => 1));

        $this->assertFalse($data);
    }


    public function testDAOGetsOneValueFromTheTableRows()
    {
        $dao      = new API\DAOs\Test_Users('test_users', $this->db);
        $data     = $dao->value(array('return' => 'username', 'id' => 1));

        $expected = new Collection(
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
                            array(
                                'username' => 'root'
                            ));

        $this->assertEquals($expected, $data);

    }

<<<<<<< HEAD
    public function testDAOValueCallReturnsEmptyArrayWhenRowDoesNotExist() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'value', array('return' => 'username', 'id' => 999), $this->db);

        $data = $dao->value();

        $expected = array();

        $this->assertEquals($data, $expected);
    }

    public function testDAOValueCallThrowsExceptionWhenValueActionParameterIsMissing() {
        $this->expectException(\Exception::class);

        $dao  = new Data\DAO(new Data\Sets($this->db), 'test_users', 'value', array('id' => 1), $this->db);

    }


    public function testDAOUpdateCallUpdatesOneTableRow() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'update', ['to_update' => 'username', 'updated' => 'updated_root', 'id' => 1], $this->db);
        
        $data = $dao->update();

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);

    }

    public function testDAOUpdateCallUpdatesAllTableRows() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'update', ['to_update' => 'is_active', 'updated' => 1], $this->db);

        $data = $dao->update();

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);
    }

    public function testDAOUpdateCallThrowsExceptionWhenUpdatedValueOnAUniqueColumn() {        
        $dao  = new Data\DAO(new Data\Sets($this->db), 'test_users', 'update', ['to_update' => 'username', 'updated' => 'root'], $this->db);
        
        $this->expectException(\Exception::class);

        $dao->update();
=======

    public function testDAOReturnsNullWhenValueActionParameterIsMissing()
    {
        $dao  = new API\DAOs\Test_Users('test_users', $this->db);
        $data = $dao->value(array('id' => 1));
        $this->assertFalse($data);

    }


    public function testDAOUpdatesOneTableValue()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);
        $dao->update(['to_update' => 'username', 'updated' => 'updated_root', 'id' => 1]);

        $value = $dao->value(['return' => 'username', 'id' => 1]);

        $expected = new Collection(
                            array(
                                'username' => 'updated_root'
                            )
                        );

        $this->assertEquals($expected, $value);

    }


    public function testDAOUpdatesAllTableRows()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);
        $dao->update(['to_update' => 'is_active', 'updated' => 1]);

        $data = $dao->get();

        foreach ($data as $value) {
            $this->assertEquals($value['is_active'], 1);
        }

    }


    public function testDAOReturnsFalseWhenUpdatedValueShouldBeUnique()
    {
        $dao  = new API\DAOs\Test_Users('test_users', $this->db);
        $data = $dao->update(['to_update' => 'username', 'updated' => 'root']);
        $this->assertFalse($data);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    }


<<<<<<< HEAD
    public function testDAOUpdateCallThrowsExceptionWhenUpdateActionParametersAreMissing() {        
        $this->expectException(\Exception::class);

        $dao  = new Data\DAO(new Data\Sets($this->db), 'test_users', 'update', ['to_update' => 'username'], $this->db);
=======
    public function testDAOReturnsNullWhenUpdateActionParametersAreMissing()
    {
        $dao  = new API\DAOs\Test_Users('test_users', $this->db);
        $data = $dao->update(['to_update' => 'username']);
        $this->assertFalse($data);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    }


<<<<<<< HEAD
    public function testDAODeleteCallDeletesOneTableRow() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'delete', ['id' => 1], $this->db);

        $data = $dao->delete();
        
        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);
=======
    public function testDAODeletesOneTableRow()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);
        $dao->delete(['id' => 1]);

        $value = $dao->value(['id' => 1]);

        $this->assertFalse($value);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    }


<<<<<<< HEAD
    public function testDeleteDAOCallDeletesAllTableRows() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'delete', array(), $this->db);

        $data = $dao->delete();

        $expected = 'Query Executed Successfully';
=======
    public function testDAODeletesAllTableRows()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $dao->delete();

        $data = $dao->get();

        $expected = new Collection();
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

        $this->assertEquals($data, $expected);

    }

<<<<<<< HEAD
    public function testDAODeleteCallThrowsExceptionWhenRowDoesNotExist() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'delete', array('id' => 999), $this->db);

        $this->expectException(\Exception::class);

        $data = $dao->delete();
    }


    public function testDAOInsertCallInsertsANewRow() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'insert',[
=======

    public function testDAOInsertNewRow()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $dao->insert(
            [
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
                'username' => 'test_user',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
<<<<<<< HEAD
            ],  $this->db);

        $data = $dao->insert();


        $expected = 'Query Executed Successfully';
=======
            ]
        );

        $data = $dao->value(['return' => 'mail', 'username' => 'test_user']);

        $expected = new Collection(
                            array(
                                'mail' => 'test_user@example.com'
                            )
                        );
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

        $this->assertEquals($data, $expected);

    }

<<<<<<< HEAD
    public function testDAOInsertCallThrowsExceptionWhenRequiredColumnParameterIsMissing() {
        $this->expectException(\Exception::class);

        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'insert', array(
                //missing username
=======
    public function testInsertReturnsFalseIfRequiredColumnValueIsMissing() {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->insert(
            [
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
<<<<<<< HEAD
            ), $this->db);

    }

    public function testDAOInsertCallThrowsExceptionWhenInsertingDuplicateValueOnAUniqueColumn() {
        $dao = new Data\DAO(new Data\Sets($this->db), 'test_users', 'insert', [
=======
            ]
        );

        $this->assertFalse($data);
    }

    public function testInsertDuplicateValueOnAUniqueColumnReturnsFalse() {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->insert(
            [
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
                'username' => 'root',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
<<<<<<< HEAD
            ], $this->db);

        $this->expectException(\Exception::class);

        $data = $dao->insert();
=======
            ]
        );

        $this->assertFalse($data);

    }

    public function testDAOReturnsNullWhenInsertActionParametersAreMissing()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->insert(
            [
                'username' => 'test_user',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'created_at' => '2018-05-24'
            ]
        );

        $this->assertFalse($data);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    }
}
