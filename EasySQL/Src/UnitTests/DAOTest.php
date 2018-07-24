<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Collection\Collection as Collection;
use EasySQL\Src\Data as Data;
use EasySQL\Src\API as API;

class DAOTest extends TestCase
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

    public function testGetCollectionOfCertainTableRows()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->get(array('id' => 1));

        $expected = new Collection(
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
                            array(
                                'username' => 'root'
                            ));

        $this->assertEquals($expected, $data);

    }


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

    }


    public function testDAOReturnsNullWhenUpdateActionParametersAreMissing()
    {
        $dao  = new API\DAOs\Test_Users('test_users', $this->db);
        $data = $dao->update(['to_update' => 'username']);
        $this->assertFalse($data);

    }


    public function testDAODeletesOneTableRow()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);
        $dao->delete(['id' => 1]);

        $value = $dao->value(['id' => 1]);

        $this->assertFalse($value);

    }


    public function testDAODeletesAllTableRows()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $dao->delete();

        $data = $dao->get();

        $expected = new Collection();

        $this->assertEquals($data, $expected);

    }


    public function testDAOInsertNewRow()
    {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $dao->insert(
            [
                'username' => 'test_user',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
            ]
        );

        $data = $dao->value(['return' => 'mail', 'username' => 'test_user']);

        $expected = new Collection(
                            array(
                                'mail' => 'test_user@example.com'
                            )
                        );

        $this->assertEquals($data, $expected);

    }

    public function testInsertReturnsFalseIfRequiredColumnValueIsMissing() {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->insert(
            [
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
            ]
        );

        $this->assertFalse($data);
    }

    public function testInsertDuplicateValueOnAUniqueColumnReturnsFalse() {
        $dao = new API\DAOs\Test_Users('test_users', $this->db);

        $data = $dao->insert(
            [
                'username' => 'root',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
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

    }
}
