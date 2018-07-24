<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Collection as Collection;
use EasySQL\Src\Data as Data;
use EasySQL\Src\API as API;

final class APITest extends TestCase
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

    }//end setUp()


    public function tearDown()
    {
        $this->db = null;
 
        $database = new Connection();

        $database->dropDatabase();

        ob_end_clean();

    }//end tearDown()

    public function testValidAPICallReturnsTheCorrectData() {

        $api = new API\API($this->db);
        $data = $api->_easy_sql('test_users', 'get', array('id' => 1));

        $expected = new Collection\Collection(
            [
                'id' => 1,
                'username' => 'root',
                'mail' => 'root@mysite.com',
                'password' => 'secret',
                'is_active' => 1,
                'role' => 'admin',
                'created_at' => '2018-05-21 21:00:00',
                'updated_at' => '2018-05-22 15:55:42'
            ]);

        $this->assertEquals($data, $expected);
    }

    public function testReturnFalseWhenWrongDataSetIsPassed() {

        $api = new API\API($this->db);
        
        $data = $api->_easy_sql('<invalid data set>', 'get', array('id' => 1));

        $this->assertFalse($data);
    }

    public function testReturnFalseWhenWrongActionIsPassed() {

        $api = new API\API($this->db);
        
        $data = $api->_easy_sql('test_users', '<invalid action>', array('id' => 1));

        $this->assertFalse($data);
    }

    public function testThrowsExceptionWhenParametersPassedIsNotAnArray() {

        $api = new API\API($this->db);

        $this->expectException(\TypeError::class);
        
        $data = $api->_easy_sql('test_users', 'get', '<invalide parameter type>');

    }

    public function testReturnNullWhenParametersPassedAreInvalid() {

        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'get', array('pet' => 'dog'));

        $this->assertFalse($data);
    }

}//end class
