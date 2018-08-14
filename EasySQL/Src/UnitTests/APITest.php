<?php

namespace EasySQL\Src\UnitTests;

use \PHPUnit\Framework\TestCase;

use EasySQL\Src\API as API;

final class APITest extends TestCase
{

    protected $db;
    protected static $database;

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

    }


    public function tearDown() {
        $this->db->query("DROP TABLE `test_users`");
        $this->db = null;
    }

    public static function tearDownAfterClass() {

        self::$database->dropDatabase();

        self::$database = null;

    }

    public function testAPICallThrowsExceptionWhenWrongDataSetIsPassed() {

        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('<invalid data set>', 'get', array('id' => 1));
    }

    public function testReturnFalseWhenWrongActionIsPassed() {

        $api = new API\API($this->db);

        $this->expectException(\Exception::class);
        
        $data = $api->_easy_sql('test_users', '<invalid action>', array('id' => 1));

    }

    public function testAPICallThrowsExceptionWhenParametersPassedIsNotAnArray() {

        $api = new API\API($this->db);

        $this->expectException(\TypeError::class);
        
        $data = $api->_easy_sql('test_users', 'get', '<invalide parameter type>');

    }

    public function testAPICallThrowsExceptionWhenParametersPassedAreInvalid() {

        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'get', array('pet' => 'dog'));

    }

    public function testAPIGetCallReturnsEmptyArrayWhenRowIsNotFound() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'get', array('id' => 999));

        $expected = array();

        $this->assertEquals($data, $expected);
    }

    public function testAPIGetCallSuccess() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'get', array('id' => 1));

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

    public function testAPIValueCallThrowsExceptionWhenActionParameterIsMissing() {

        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'value', array('id' => 1));
    }

    public function testAPIValueCallReturnsEmptyArrayWhenRowIsNotFound() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'value', array('return' => 'username', 'id' => 999));

        $expected = array();

        $this->assertEquals($data, $expected);
    }

    public function testAPIValueCallThrowsExceptionWhenFieldToReturnDoesNotExist() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'value', array('return' => 'bogus_field', 'id' => 1));
    }

    public function testAPIValueCallReturnOneFieldSuccess() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'value', array('return' => 'username', 'id' => 1));

        $expected = array(
            array(
                'username' => 'root'
            )
        );

        $this->assertEquals($data, $expected);
    }

    public function testAPIValueCallReturnMultipleFieldsSuccess() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'value', array('return' => array('username', 'password'), 'id' => 1));

        $expected = array(
            array(
                'username' => 'root',
                'password' => 'secret'
            )
        );

        $this->assertEquals($data, $expected);
    }

    public function testAPIUpdateCallThrowsExceptionWhenActionParameterIsMissing() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'update', array('to_update' => 'username', 'id' => 1));
    }

    public function testAPIUpdateCallThrowsExceptionWhenParameterFieldPassedDoesNotExist() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'update', array('to_update' => 'username', 'updated' => 'test_user', 'bogus_parameter' => 1));
    }

    public function testAPIUpdateCallThrowsExceptionWhenRowToUpdateDoesNotExist() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'update', array('to_update' => 'username', 'updated' => 'test_user', 'id' => 999));
    }

    public function testAPIUpdateCallThrowsExceptionWhenUpdateDuplicatedValueOnUniqueColumn() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'update', array('to_update' => 'username', 'updated' => 'root', 'id' => 2));
    }

    public function testAPIUpdateCallThrowsExceptionWhenFieldToUpdateDoesNotExist() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'update', array('to_update' => 'bogus_field', 'updated' => 1));        
    }


    public function testAPIUpdateCallSuccess() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'update', array('to_update' => 'username', 'id' => 1, 'updated' => 'admin'));

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);
    }

    public function testAPIDeleteCallThrowsExceptionWhenRowToDeleteDoesNotExist() {
        $api = new API\API($this->db);

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'delete', array('id' => 999));
    }

    public function testAPIDeleteCallSuccess() {
        $api = new API\API($this->db);

        $data = $api->_easy_sql('test_users', 'delete', array('id' => 1));

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);
    }

    public function testAPIInsertCallThrowsExceptionWhenRequiredColumnParameterIsMissing() {
        $api = new API\API($this->db);

        $params = array(
            //missing username
            'mail' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'role' => 'user'
        );

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'insert', $params);
    }

    public function testAPIInsertCallThrowsExceptionWhenDuplicatedValueOnUniqueColumn() {
        $api = new API\API($this->db);

        $params = array(
            'username' => 'root', //Duplicate
            'mail' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'role' => 'user'
        );

        $this->expectException(\Exception::class);

        $data = $api->_easy_sql('test_users', 'insert', $params);
    }

    public function testAPIInsertCallSuccess() {
        $api = new API\API($this->db);

        $params = array(
            'username' => 'test_user', //Duplicate
            'mail' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'role' => 'user'
        );

        $data = $api->_easy_sql('test_users', 'insert', $params);

        $expected = 'Query Executed Successfully';

        $this->assertEquals($data, $expected);
    }
}
