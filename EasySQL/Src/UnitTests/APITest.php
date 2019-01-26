<?php

namespace EasySQL\Src\UnitTests;

use \PHPUnit\Framework\TestCase;

use EasySQL\Src\API\API;

final class APITest extends TestCase
{

    protected $db;
    protected static $database;

    public static function setUpBeforeClass()
    {
        self::$database = new Connection();
        self::$database->createDatabase();

    }    

    public function setUp()
    {

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


    public function tearDown()
    {
        $this->db->query("DROP TABLE `test_users`");
        $this->db = null;
    }

    public static function tearDownAfterClass()
    {

        self::$database->dropDatabase();

        self::$database = null;

    }

    public function testAPICallThrowsExceptionWhenWrongDataSetIsPassed()
    {        
        $this->expectOutputString('Table invalid was not found.');
        
        (new API($this->db))->get('invalid')->where(['id' => 1]);
    }
    
    public function testReturnFalseWhenWrongActionIsPassed()
    {
        $this->expectException(\Error::class);
        
        (new API($this->db))->invalid('invalid')->where(['id' => 1])->execute();
    }

    
    public function testAPICallThrowsExceptionWhenParametersPassedIsNotAnArray()
    {
        $this->expectException(\TypeError::class);
        
        (new API($this->db))->get('test_users')->where('hello');
    }

    
    public function testAPICallThrowsExceptionWhenParametersPassedAreInvalid()
    {
        $this->expectOutputString('Field pet was not found.');
        
        (new API($this->db))->get('test_users')->where(['pet' => 'dog']);
    }
    
    
    public function testAPIGetCallReturnsEmptyArrayWhenRowIsNotFound()
    {

        $data = (new API($this->db))->get('test_users')->where(['id' => 999])->execute();
        
        $expected = array();

        $this->assertEquals($data, $expected);
    }
    
    
    public function testAPIGetCallReturnsCorrectResults()
    {
        $data = (new API($this->db))->get('test_users')->where(['id' => 1])->execute();

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
   
    
    public function testAPIGetCallThrowsExceptionWhenFieldToReturnDoesNotExist()
    {
        $this->expectOutputString('Field bogus was not found.');

        (new API($this->db))->get('test_users')->return('bogus');
    }
    
    
    public function testAPIGetCallReturnOneFieldSuccessfully()
    {
        $data = (new API($this->db))->get('test_users')->return('username')->where(['id' => 1])->execute();

        $expected = array(
            array(
                'username' => 'root'
            )
        );

        $this->assertEquals($data, $expected);
    }
    
    public function testAPIGetCallReturnMultipleFieldsSuccessfully()
    {
        $data = (new API($this->db))->get('test_users')->return('username', 'password')->where(['id' => 1])->execute();

        $expected = array(
            array(
                'username' => 'root',
                'password' => 'secret'
            )
        );

        $this->assertEquals($data, $expected);
    }

    public function testAPIUpdateCallThrowsExceptionWhenParameterIsMissing()
    {
        $this->expectException(\ArgumentCountError::class);

        (new API($this->db))->update('test_users')->set('username')->where(['id' => 1])->execute();
    }
    
    public function testAPIUpdateCallThrowsExceptionWhenRowToUpdateDoesNotExist()
    {
        $this->expectOutputString('Field bogus was not found.');

        (new API($this->db))->update('test_users')->set('bogus', 'test');
    }
    
    public function testAPIUpdateCallThrowsExceptionWhenUpdateDuplicatedValueOnUniqueColumn()
    {
        $this->expectOutputString('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'root\' for key \'username\'');

        (new API($this->db))->update('test_users')->set('username', 'root')->where(['id' => 2])->execute();
    }
    
    public function testAPIUpdateCallSuccess()
    {
        $data = (new API($this->db))->update('test_users')->set('username', 'admin')->where(['id' => 1])->execute();

        $expected = [];

        $this->assertEquals($data, $expected);
    }
    
    public function testAPIDeleteCallSuccess()
    {
        $data = (new API($this->db))->delete('test_users')->where(['id' => 1])->execute();

        $expected = [];

        $this->assertEquals($data, $expected);
    }
    
    public function testAPIInsertCallThrowsExceptionWhenRequiredColumnParameterIsMissing()
    {
        $params = array(
            //missing username
            'mail' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'role' => 'user'
        );

        $this->expectOutputString('Missing Required Fields (username)');

        (new API($this->db))->insert('test_users')->values($params);
    }
    
    public function testAPIInsertCallThrowsExceptionWhenDuplicatedValueOnUniqueColumn()
    {
        $params = array(
            'username' => 'root', //Duplicate
            'mail' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'role' => 'user'
        );

        $this->expectOutputString('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'root\' for key \'username\'');

        (new API($this->db))->insert('test_users')->values($params)->execute();
    }
    
    public function testAPIInsertCallSuccess()
    {
        $params = array(
            'username' => 'test_user', //Duplicate
            'mail' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'role' => 'user'
        );

        $data = (new API($this->db))->insert('test_users')->values($params)->execute();

        $this->assertEquals([], $data);
    }
}
