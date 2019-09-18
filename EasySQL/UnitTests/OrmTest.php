<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UnitTests;

/**
 * Description of OrmTest
 *
 * @author gatas
 */
class OrmTest extends TestCase
{
    
    public function testBasicRetrieveMethods()
    {
        // Test retrieve all
        $movies = new Movies(self::$connection);        
        $movies_list = $movies->all();
        $this->assertEquals(5, count($movies_list));
        
        // Test retrieve first record
        $movies = new Movies(self::$connection);
        $movie = $movies->first();
        $this->assertEquals(1, $movie->id);

        // Test retrieve last record
        $movies = new Movies(self::$connection);
        $movie = $movies->last();
        $this->assertEquals(5, $movie->id);

        // Test filter by single word column
        $movies = new Movies(self::$connection);
        $movie = $movies->filterById(5)->all();
        $this->assertEquals('Deadpool 2', $movie[0]->title);

        // Test filter by concatenated words column with underscore
        $extras = new Extra(self::$connection);
        $extra = $extras->filterByMovieId(1)->first();
        $this->assertEquals(1, $extra->id);
    }
    
    public function testAdvancedRetrieveMethods()
    {
        // Test grouping records by column
        $extra = new Extra(self::$connection);        
        $info = $extra->group('certificate')->all();
        $this->assertEquals(2, count($info));

        // Test retrieving record if column is in a set of values
        // given by a subquery
        $movies = new Movies(self::$connection);
        $extra = new Extra(self::$connection);
        $subquery = $extra->select('movie_id')->filterByCertificate('PG-13');
        $movie = $movies->in('id', $subquery)->all();
        $this->assertEquals(3, count($movie));

        // Test retrieving records if a record returned by a
        // subquery exists in the database
        $movies = new Movies(self::$connection);
        $extra = new Extra(self::$connection);
        $subquery = $extra->select('movie_id')->filterByCertificate('R');
        $movie = $movies->exists($subquery)->all();
        $this->assertEquals(5, count($movie));
    }

    public function testRelationshipMethods() 
    {
        // Relationships using join

        // One to One
        $movies = new Movies(self::$connection);        
        $movie = $movies->join('extra', 'movies.id', '=', 'movie_id')->all();
        $this->assertEquals(4, count($movie));
        $this->assertEquals(1, count($movie[0]->extra));

        // One to Many
        $studios = new Studios(self::$connection);        
        $studio = $studios->join('movies', 'studios.id', '=', 'studio_id')->all();
        $this->assertEquals(3, count($studio[0]->movies));

        // Many To Many
        $movies = new Movies(self::$connection);        
        $movie = $movies->join('movies_actors', 'movies.id', '=', 'movie_id', true)->join('actors', 'actors.id', '=', 'actor_id')->all();
        $this->assertEquals(3, count($movie[0]->actors));

        // Relationships using relationships trait

        // One to One
        $movies = new Movies(self::$connection);
        $movie = $movies->filterById(1)->first();
        $extra = $movie->ownerOfOne('extra');
        $this->assertEquals(1, $extra->movie_id);

        $movies = new Movies(self::$connection);
        $movie = $movies->filterById(1)->first();
        $extra = $movie->ownerOfOne('extra', 'id', 'movie_id');
        $this->assertEquals(1, $extra->movie_id);

        $extras = new Extra(self::$connection);
        $extra = $extras->filterByMovieId(1)->first();
        $movie = $extra->memberOfOne('movies');
        $this->assertEquals(1, $movie->id);

        $extras = new Extra(self::$connection);
        $extra = $extras->filterByMovieId(1)->first();
        $movie = $extra->memberOfOne('movies', 'movie_id', 'id');
        $this->assertEquals(1, $movie->id);

        // One to Many
        $studios = new Studios(self::$connection);
        $studio = $studios->filterById(1)->first();
        $movies = $studio->ownerOfMany('movies');
        $this->assertEquals(3, count($movies));

        $studios = new Studios(self::$connection);
        $studio = $studios->filterById(1)->first();
        $movies = $studio->ownerOfMany('movies', 'id', 'studio_id');
        $this->assertEquals(3, count($movies));

        // Many to Many
        $actors = new Actors(self::$connection);
        $actor = $actors->filterById(5)->first();
        $movies = $actor->membersHaveMany('movies');
        $this->assertEquals(2, count($movies));
    }
    
    public function testModificationMethods()
    {
        
        $movies = new Movies(self::$connection);
        $movies->title = 'Venom';
        $movies->released = '2018-10-05';
        $movies->gross = 213520000;
        $movies->studio_id = 4;
        $movies->save();
        $movies = new Movies(self::$connection);
        $last = $movies->last();
        $this->assertEquals('Venom', $last->title);

        $movies = new Movies(self::$connection);
        $movie = $movies->filterById(1)->first();
        $movie->title = 'Avengers Updated Title';
        $movie->save();
        $movies = new Movies(self::$connection);
        $movie = $movies->first();
        $this->assertEquals('Avengers Updated Title', $movie->title);

        $movies = new Movies(self::$connection);
        $movies->id = 3;
        $movies->delete();
        $movies = new Movies(self::$connection);
        $first = $movies->where('id', '=', 3)->all();
        $this->assertEmpty($first);
    }
}
