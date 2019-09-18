<?php

namespace UnitTests;

use \EasySQL\Query\DB;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QueryBuilderTest
 *
 * @author gatas
 */
class QueryBuilderTest extends TestCase
{

    public function testBasicRetrieveMethods()
    {
        DB::$connection = self::$connection;
        $movies = DB::table('movies')->select('*')->get();
        $this->assertEquals(5, count($movies));
        
        $movie = DB::table('movies')->select('*')->where('id', '=', 2)->get();
        $this->assertEquals(1, count($movie));

        $movies = DB::table('movies')->select('*')->where('released', '>', '2018-01-01 00:00:00')->and()->where('released', '<', '2018-06-01 00:00:00')->get();
        $this->assertEquals(3, count($movies));
        
        $movie = DB::table('movies')->select('*')->limit(1)->order('title', 'DESC')->get();
        $this->assertEquals(1, count($movie));
        $this->assertEquals('Deadpool 2', $movie[0]['title']);
        
        $title = DB::table('movies')->select('*')->where('id', '=', 2)->find('title');
        $this->assertEquals('Aquaman', $title[0]['title']);
        
        $movies = DB::table('movies')->select('*')->between('gross', 250000000, 750000000)->get();
        $this->assertEquals(4, count($movies));
        
        $first = DB::table('movies')->select('*')->first();
        $this->assertEquals(1, $first[0]['id']);

        $last = DB::table('movies')->select('*')->last();
        $this->assertEquals(5, $last[0]['id']);
        
        $max_gross = DB::table('movies')->max('gross');
        $this->assertEquals(700060000, $max_gross);
    
        $min_gross = DB::table('movies')->min('gross');
        $this->assertEquals(216650000, $min_gross);
        
        $avg_gross = DB::table('movies')->avg('gross');
        $this->assertEquals(451036000, $avg_gross);
        
        $sum_gross = DB::table('movies')->sum('gross');
        $this->assertEquals(2255180000, $sum_gross);
    }
    
    public function testAdvancedRetrieveMethods()
    {
        $movies_info = DB::table('movies')->select('*')->join('extra', 'movies.id', '=', 'movie_id')->get();
        $this->assertEquals(4, count($movies_info));
        
        $movies_info = DB::table('movies')->select('*')->leftJoin('extra', 'movies.id', '=', 'movie_id')->get();
        $this->assertEquals(5, count($movies_info));
        
        //$movies_info = DB::table('movies')->select('*')->rightJoin('extra', 'movies.id', '=', 'movie_id')->get();
        //$this->assertEquals(4, count($movies_info));
        
        $grouped = DB::table('movies')->select('*')->join('extra', 'movies.id', '=', 'movie_id')->group('certificate')->get();
        $this->assertEquals(2, count($grouped));
        
        //$grouped = DB::table('movies')->select('*')->join('extra', 'movies.id', '=', 'movie_id')->group('certificate')->having('duration', '<', 120)->get();
        //$this->assertEquals(2, count($grouped));
        
        $subquery = DB::table('extra')->select('movie_id');
        $movies = DB::table('movies')->select('*')->where('id', 'IN', $subquery)->get();
        $this->assertEquals(4, count($movies));
        
        $subquery = DB::table('extra')->select('movie_id')->where('movie_id', '=', 3);
        
        $movie = DB::table('movies')->select('*')->in('id', $subquery)->get();
        $this->assertEmpty($movie);

        $movie = DB::table('movies')->select('*')->exists($subquery)->get();
        $this->assertEmpty($movie);

    }
    
    public function testModificationMethods()
    {
        DB::table('movies')->insert(['title' => 'Venom', 'released' => '2018-10-05', 'gross' => 213520000, 'studio_id' => 4]);
        $last = DB::table('movies')->select('*')->last();
        $this->assertEquals('Venom', $last[0]['title']);
        
        DB::table('movies')->where('id', '=', 3)->delete();
        $movie = DB::table('movies')->select('*')->where('id', '=', 3)->get();
        $this->assertEmpty($movie);
        
        DB::table('movies')->where('id', '=', 4)->update('gross', 200000000);
        $movie = DB::table('movies')->where('id', '=', 4)->find('gross');
        $this->assertEquals(200000000, $movie[0]['gross']);
    }
}
