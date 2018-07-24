<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Collection\Collection as Collection;
use EasySQL\Src\Data as Data;

final class SQLSTest extends TestCase
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

    }


    public function tearDown()
    {
        $this->db = null;
        
        $database = new Connection();

        $database->dropDatabase();
        
        ob_end_clean();
    }

    public function testExecuteQueryReturnsCorrectCollection() {
        $sql = new Data\SQL($this->db);

        $query = 'SELECT username FROM test_users WHERE id=?';

        $params = array('id' => 1);

        $data = $sql->executeQuery($query, $params);

        $expected = new Collection(array('username' => 'root'));

        $this->assertEquals($data, $expected);
    }

    public function testExecuteMisstypedQueryThrowsPDOException() {
        $sql = new Data\SQL($this->db);

        $query = 'INVALID QUERY';

        $this->expectException(\PDOException::class);

        $data = $sql->executeQuery($query);
    }

    public function testExecuteQueryThrowsPDOExceptionWhenParametersCountIsDifferentThanThoseInTheQuery() {
        $sql = new Data\SQL($this->db);

        $query = 'SELECT * FROM test_users WHERE id = ? AND username = ?';

        $params = array('id' => 1);
        
        $this->expectException(\PDOException::class);

        $data = $sql->executeQuery($query, $params);
    }
}
