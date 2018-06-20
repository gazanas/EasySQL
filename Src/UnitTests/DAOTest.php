<?php

namespace Src\UnitTests;

use PHPUnit\Framework\TestCase;

use Src\Collection\Collection as Collection;
use Src\Data as Data;
use Src\API as API;

class DAOTest extends TestCase
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


    public function testCorrectDAOCallReturnsACollectionObject()
    {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

        $data = $dao->get();

        $this->assertInstanceOf(Collection::class, $data);

    }

    public function testGetCollectionOfAllTableRows()
    {
        $dao  = new API\DAOs\Test_Users($this->config, 'test_users');
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
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

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
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

        $data = $dao->get(array('<invalid parameter>' => 1));

        $this->assertFalse($data);
    }


    public function testDAOGetsOneValueFromTheTableRows()
    {
        $dao      = new API\DAOs\Test_Users($this->config, 'test_users');
        $data     = $dao->value(array('return' => 'username', 'id' => 1));

        $expected = new Collection(
                            array(
                                'username' => 'root'
                            ));

        $this->assertEquals($expected, $data);

    }//end testDAOGetsOneTableValue()


    public function testDAOReturnsNullWhenValueActionParameterIsMissing()
    {
        $dao  = new API\DAOs\Test_Users($this->config, 'test_users');
        $data = $dao->value(array('id' => 1));
        $this->assertNull($data);

    }//end testDAOReturnsNullWhenValueActionParameterIsMissing()


    public function testDAOUpdatesOneTableValue()
    {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');
        $dao->update(['to_update' => 'username', 'updated' => 'updated_root', 'id' => 1]);

        $value = $dao->value(['return' => 'username', 'id' => 1]);

        $expected = new Collection(
                            array(
                                'username' => 'updated_root'
                            )
                        );

        $this->assertEquals($expected, $value);

    }//end testDAOUpdatesOneTableValue()


    public function testDAOUpdatesAllTableRows()
    {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');
        $dao->update(['to_update' => 'is_active', 'updated' => 1]);

        $data = $dao->get();

        foreach ($data as $value) {
            $this->assertEquals($value['is_active'], 1);
        }

    }//end testDAOUpdatesAllTableRows()


    public function testDAOReturnsFalseWhenUpdatedValueShouldBeUnique()
    {
        $dao  = new API\DAOs\Test_Users($this->config, 'test_users');
        $data = $dao->update(['to_update' => 'username', 'updated' => 'root']);
        $this->assertFalse($data);

    }//end testDAOReturnsFalseWhenUpdatedValueShouldBeUnique()


    public function testDAOReturnsNullWhenUpdateActionParametersAreMissing()
    {
        $dao  = new API\DAOs\Test_Users($this->config, 'test_users');
        $data = $dao->update(['to_update' => 'username']);
        $this->assertNull($data);

    }//end testDAOReturnsNullWhenUpdateActionParametersAreMissing()


    public function testDAODeletesOneTableRow()
    {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');
        $dao->delete(['id' => 1]);

        $value = $dao->value(['id' => 1]);

        $this->assertNull($value);

    }//end testDAODeletesOneTableRow()


    public function testDAODeletesAllTableRows()
    {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

        $dao->delete();

        $data = $dao->get();

        $expected = new Collection();

        $this->assertEquals($data, $expected);

    }//end testDAODeletesAllTableRows()


    public function testDAOInsertNewRow()
    {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

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
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

        $data = $dao->insert(
            [
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'role' => 'user',
                'created_at' => '2018-05-24'
            ]
        );

        $this->assertNull($data);
    }

    public function testInsertDuplicateValueOnAUniqueColumnReturnsFalse() {
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

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
        $dao = new API\DAOs\Test_Users($this->config, 'test_users');

        $data = $dao->insert(
            [
                'username' => 'test_user',
                'mail' => 'test_user@example.com',
                'password' => 'secret',
                'is_active' => 0,
                'created_at' => '2018-05-24'
            ]
        );

        $this->assertNull($data);

    }//end testDAOReturnsNullWhenInsertActionParametersAreMissing()


}//end class
