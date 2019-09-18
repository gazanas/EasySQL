<?php

namespace UnitTests;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestCase
 *
 * @author gatas
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    public static $connection;
    public static $dbms;

    public static function setUpBeforeClass()
    {
        global $argv, $argc;
        if ($argv[2] != 'mysql' && $argv[2] != 'sqlite') {
            print("Accepted drivers are mysql and sqlite");
            exit(0);
        }
        self:: $dbms = $argv[2];
        $populator = (new CinemaTest\CinemaPopulator());
        if (self::$dbms == 'sqlite') {
            self::$connection = (new \EasySQL\Drivers\SqliteConnection())->connect('sqlite', ':memory:');
            $populator->createDatabase(self::$connection, self::$dbms);
        } else {
            $connection = (new \EasySQL\Drivers\MysqlConnection())->connectNoDatabase('mysql', 'localhost', 'root', null);            
            $populator->createDatabase($connection, self::$dbms);
            self::$connection = (new \EasySQL\Drivers\MysqlConnection())->connect('mysql', 'localhost', 'root', 'cinema', null);
        }
        $populator->tableFixture(self::$connection, self::$dbms);
    }
    
    public function setUp()
    {
        (new CinemaTest\CinemaPopulator())->populate(self::$connection);
    }
    
    public function tearDown()
    {
        (new CinemaTest\CinemaPopulator())->depopulate(self::$connection, self::$dbms);
        \EasySQL\Query\DB::$dependencies = [];
    }
    
    public static function tearDownAfterClass()
    {   
        if (self::$dbms == 'mysql')
            (new CinemaTest\CinemaPopulator())->dropTable(self::$connection);
    }
}
