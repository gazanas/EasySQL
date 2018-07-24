<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Data\SQL as SQL;
use EasySQL\Src\Data\Parameters as Parameters;
use EasySQL\Src\Data as Data;

final class ParametersTest extends TestCase
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

    }//end setUp()


    public function tearDown()
    {
        $this->db = null;
        
        $database = new Connection();

        $database->dropDatabase();
        
        ob_end_clean();

    }//end tearDown()

    public function testPrepareParametersArrayPassedByTheUser()
    {
        $parameters = new Parameters($this->db);

        $prepared = $parameters->prepareParameters('SELECT * FROM test_users', array('id' => 1, 'username' => 'root'));

        $expected = array(
            1,
            'root'
        );

        $this->assertSame($prepared, $expected);
    }//end testReturnTrueWhenActionIsInTheActionSet()

    public function testReturnNullIfWrongTypeOfParameterPassed() {
        $parameters = new Parameters($this->db);

        $prepared = $parameters->prepareParameters('SELECT * FROM test_users', 'root');

        $this->assertNull($prepared);
    }

    public function testReturnNullIfEmptyParametersArrayPassed() {
        $parameters = new Parameters($this->db);

        $prepared = $parameters->prepareParameters('SELECT * FROM test_users', array());

        $this->assertNull($prepared);
    }

}//end class
