<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Parameters as Parameters;
use EasySQL\Src\Sets as Sets;

final class ParametersTest extends TestCase
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

    public function testPrepareGetParametersByValidParametersArrayPassedByTheUser()
    {
        $parameters = new Parameters\GetParameters($this->sets);

        $prepared = $parameters->prepareParameters('test_users', array('id' => 1, 'username' => 'root'));

        $expected = array(
            1,
            'root'
        );

        $this->assertSame($prepared, $expected);
    }

    public function testPrepareGetParametersThrowsExceptionWhenWrongTypeOfParameterPassed() {
        $parameters = new Parameters\GetParameters($this->sets);

        $this->expectException(\Exception::class);

        $prepared = $parameters->prepareParameters('test_users', 'root');

    }

    public function testPrepareGetParametersReturnEmptyArrayIfEmptyParametersArrayPassed() {
        $parameters = new Parameters\GetParameters($this->sets);

        $prepared = $parameters->prepareParameters('test_users', array());

        $expected = array();

        $this->assertSame($prepared, $expected);
    }

    public function testPrepareGetParametersWithReturnParameter() {
        $parameters = new Parameters\GetParameters($this->sets);

        $prepared = $parameters->prepareParameters('test_users', array('return' => 'username', 'id' => 1));

        $expected = array(1);

        $this->assertSame($prepared, $expected);        

    }

    public function testPrepareParametersForUpdateActionThrowsExceptionWhenActionParameterIsMissing() {
        $parameters = new Parameters\UpdateParameters($this->sets);

        $this->expectException(\Exception::class);

        $prepared = $parameters->prepareParameters('test_users', array('to_update' => 'username', 'id' => 1));
    }

    public function testPreapreParametersForInsertAction() {
        $parameters = new Parameters\InsertParameters($this->sets);

        $params = array(
            'username' => 'test_user',
            'mail' => 'test@example.com',
            'password' => 'secret',
            'role' => 'user',
            'is_active' => 1
        );

        $preparedParameters = $parameters->prepareParameters('test_users', $params);

        $expected = array(
            'test_user',
            'test@example.com',
            'secret',
            'user',
            1
        );

        $this->assertEquals($preparedParameters, $expected);
    }

    public function testPrepareParametersForInsertActionThrowsExceptionWhenRequiredColumnIsMissing() {
        $parameters = new Parameters\InsertParameters($this->sets);

        $params = array(
            //username missing
            'mail' => 'test@example.com',
            'password' => 'secret',
            'role' => 'user',
            'is_active' => 1
        );

        $this->expectException(\Exception::class);
    
        $prepared = $parameters->prepareParameters('test_users', $params);
    }
}
