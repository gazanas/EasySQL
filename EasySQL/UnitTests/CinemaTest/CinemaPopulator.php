<?php

namespace UnitTests\CinemaTest;

use \EasySQL\Query\DB;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CinemaPopulator
 *
 * @author gatas
 */
class CinemaPopulator
{
    
    
    public function createDatabase($connection, string $dbms)
    {
        if ($dbms == "sqlite") {
            $stmt = $connection->prepare(
            "
                ATTACH DATABASE \":memory:\" AS cinema
                "
            );
        } else {
            $stmt = $connection->prepare(
                "
                CREATE DATABASE cinema
            "
            );
        }
        $stmt->execute();
    }
    
    public function tableFixture($connection, string $dbms)
    {
        if ($dbms == "mysql") {
            $i = "int";
            $auto = "AUTO_INCREMENT";
        } else {
            $i = "integer";
            $auto = "AUTOINCREMENT";
        }

        $stmt = $connection->prepare(
            "
                CREATE TABLE studios(
                id {$i} not null primary key {$auto},
                name varchar(100) not null
                )
                "
        );

        $stmt->execute();

        $stmt = $connection->prepare(
            "
                CREATE TABLE movies(
                id {$i} not null primary key {$auto},
                studio_id {$i} not null,
                title varchar(100) not null,
                released timestamp DEFAULT CURRENT_TIMESTAMP,
                gross {$i},
                FOREIGN KEY (studio_id) REFERENCES studios(id) ON DELETE CASCADE ON UPDATE NO ACTION
                )
                "
        );
        
        $stmt->execute();

        $stmt = $connection->prepare(
            "
                CREATE TABLE extra(
                id {$i} not null primary key {$auto},
                movie_id {$i} not null,
                certificate varchar(10) not null,
                duration {$i} not null,
                FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE ON UPDATE NO ACTION
                )
                "
        );

        $stmt->execute();

        $stmt = $connection->prepare(
            "
                CREATE TABLE actors(
                id {$i} not null primary key {$auto},
                name varchar(50) not null
                )
                "
        );

        $stmt->execute();

        $stmt = $connection->prepare(
            "
                CREATE TABLE movies_actors(
                id {$i} not null primary key {$auto},
                movie_id {$i} not null,
                actor_id {$i} not null,
                FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE ON UPDATE NO ACTION,
                FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE ON UPDATE NO ACTION
                )
                "
        );

        $stmt->execute();
    }
    
    public function dropTable($connection)
    {
        $stmt = $connection->prepare(
            "
                DROP DATABASE cinema
                "
        );
        $stmt->execute();
    }
    
    public function populate($connection)
    {

        DB::$connection = $connection;
        DB::table("studios")->insert(["name" => "Walt Disney Studios Motion Pictures"]);
        DB::table("studios")->insert(["name" => "Warner Bros. Pictures"]);
        DB::table("studios")->insert(["name" => "Marvel Entertainment"]);
        DB::table("studios")->insert(["name" => "Sony Pictures Releasing"]);

        DB::table("movies")->insert(["title" => "Avengers: Infinity War", "released" => "2018-04-27", "gross" => 678820000, "studio_id" => 1]);
        DB::table("movies")->insert(["title" => "Aquaman", "released" => "2018-12-21", "gross" => 335060000, "studio_id" => 2]);
        DB::table("movies")->insert(["title" => "Black Panther", "released" => "2018-02-16", "gross" => 700060000, "studio_id" => 1]);
        DB::table("movies")->insert(["title" => "Ant-Man and the Wasp", "released" => "2018-07-06", "gross" => 216650000, "studio_id" => 1]);
        DB::table("movies")->insert(["title" => "Deadpool 2", "released" => "2018-05-18", "gross" => 324590000, "studio_id" => 3]);
    
        DB::table("extra")->insert(["movie_id" => 1, "certificate" => "PG-13", "duration" => 149]);
        DB::table("extra")->insert(["movie_id" => 2, "certificate" => "PG-13", "duration" => 143]);
        //DB::table("extra")->insert(["movie_id" => 3, "certificate" => "PG-13", "duration" => 134]);
        DB::table("extra")->insert(["movie_id" => 4, "certificate" => "PG-13", "duration" => 118]);
        DB::table("extra")->insert(["movie_id" => 5, "certificate" => "R", "duration" => 119]);

        DB::table("actors")->insert(["name" => "Robert Downey Jr."]);
        DB::table("actors")->insert(["name" => "Chris Hemsworth"]);
        DB::table("actors")->insert(["name" => "Jason Momoa"]);
        DB::table("actors")->insert(["name" => "Amber Heard"]);
        DB::table("actors")->insert(["name" => "Chadwick Boseman"]);

        DB::table("movies_actors")->insert(["movie_id" => 1, "actor_id" => 1]);
        DB::table("movies_actors")->insert(["movie_id" => 1, "actor_id" => 2]);
        DB::table("movies_actors")->insert(["movie_id" => 1, "actor_id" => 5]);
        DB::table("movies_actors")->insert(["movie_id" => 2, "actor_id" => 3]);
        DB::table("movies_actors")->insert(["movie_id" => 2, "actor_id" => 4]);
        DB::table("movies_actors")->insert(["movie_id" => 3, "actor_id" => 5]);

    }
    
    public function depopulate($connection, string $dbms)
    {
        DB::$connection = $connection;
        DB::table("studios")->delete();
        DB::table("extra")->delete();
        DB::table("movies_actors")->delete();
        DB::table("movies")->delete();
        DB::table("actors")->delete();
        
        if ($dbms == "sqlite") {
            DB::table("studios")->raw("DELETE FROM sqlite_sequence WHERE name=\"studios\"");
            DB::table("extra")->raw("DELETE FROM sqlite_sequence WHERE name=\"extra\"");
            DB::table("movies_actors")->raw("DELETE FROM sqlite_sequence WHERE name=\"movies_actors\"");
            DB::table("movies")->raw("DELETE FROM sqlite_sequence WHERE name=\"movies\"");
            DB::table("actors")->raw("DELETE FROM sqlite_sequence WHERE name=\"actors\"");
        } else {
            DB::table("studios")->raw("ALTER TABLE studios AUTO_INCREMENT=0");
            DB::table("extra")->raw("ALTER TABLE extra AUTO_INCREMENT=0");
            DB::table("movies_actors")->raw("ALTER TABLE movies_actors AUTO_INCREMENT=0");
            DB::table("movies")->raw("ALTER TABLE movies AUTO_INCREMENT=0");
            DB::table("actors")->raw("ALTER TABLE actors AUTO_INCREMENT=0");
        }
    }
}
