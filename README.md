# Read Me

EasySQL is a PHP SQL API/ORM that allows users to perform SQL queries
in a one liner code. EasySQL uses prepared statements to avoid SQL injections.

# Installation

Prerequisites: SQL DBMS (mysql, sqlite are supported), php, PDO module.

To install EasySQL you need to install the modules needed from the directory
where composer.json is, by executing:

```
composer install
```

Create a database.

Write the configuration file on .env/database/config.ini

- MySQL example config:

```
dbms = mysql
host = localhost
username = root
database = easysql
password = password
```

- SQLite example config:

```
dbms = sqlite
file = easysql.sq3
username =
password = 
```

```
use EasySQL\Query\DB;

/**
 * Default configuration is EasySQL/.env/database/config.ini
 * If you want to change it then do:
 * DB::$config = 'your_configuration_file';
 * Also you could pass your own \PDO connection object in this case
 * the configuration file will be ignored
 * DB::$connection = new \PDO($dsn, $username, $password, $options);
 */

DB::table('users')->select('*')->get();
```

To run unit tests:

```
phpunit EasySQL\Src\UnitTests sqlite

or

phpunit EasySQL\Src\UnitTest mysql
```

# Query Builder Usage

Let's say we have this table named users
```
+------------+------------------+------+-----+-------------------+----------------+
| Field      | Type             | Null | Key | Default           | Extra          |
+------------+------------------+------+-----+-------------------+----------------+
| id         | int(10) unsigned | NO   | PRI | NULL              | auto_increment |
| username   | varchar(15)      | NO   | UNI | NULL              |                |
| mail       | varchar(320)     | NO   | UNI | NULL              |                |
| password   | varchar(60)      | NO   |     | NULL              |                |
| is_active  | tinyint(1)       | NO   |     | 0                 |                |
| role       | varchar(10)      | NO   |     | user              |                |
| created_at | timestamp        | YES  |     | NULL              |                |
| updated_at | timestamp        | YES  |     | CURRENT_TIMESTAMP |                |
+------------+------------------+------+-----+-------------------+----------------+
```
That contains these values (never store passwords in clear text):
```
+----+----------+------------------+----------+-----------+-------+---------------------+---------------------+
| id | username | mail             | password | is_active | role  | created_at          | updated_at          |
+----+----------+------------------+----------+-----------+-------+---------------------+---------------------+
|  1 | admin    | admin@mysite.com | secret   |         1 | admin | 2018-05-25 00:00:00 | 2018-06-09 12:53:36 |
|  2 | dani     | dani@example.com | terces   |         0 | user  | 2018-05-30 00:00:00 | 2018-06-06 00:00:00 |
+----+----------+------------------+----------+-----------+-------+---------------------+---------------------+
```
Now we want to perform some action using the EasySQL API.

### BASIC USAGE

- Get all the columns of the table:

```
DB::table('users')->select('*')->get();
```

- Get certain columns of the table:

```
DB::table('users')->find('username', 'role');
DB::table('users')->select('username', 'role')->get();
```

- Get the first row:

```
DB::table('users')->select('*')->first();
```

- Get the last row:

```
DB::table('users')->select('*')->last();
```

- Filter the values of the rows:

```
DB::table('users')->where('id', '=', 3)->get();
```

```
DB::table('users')->where('id', '>', 1)->and()->where('username', '=', 'dani')->get();

is identical to

DB::table('users')->where('id', '>', 1)->where('username', '=', 'dani')->get();
```

```
$builder->where('id', '>', 1)->or()->where('username', '=', 'admin')->get();
```

```
DB::table('users')->limit(1)->get();
```

```
DB::table('users')->order('username', 'ASC')->get();
```

```
DB::table('users')->group('role')->having('id', '>', 1)->get();
```

- Aggregate functions

```
DB::table('users')->max('id');
DB::table('users')->min('id');
DB::table('users')->sum('id');
DB::table('users')->avg('id');
DB::table('users')->count('id');
```

- Insert new row (will automatically set null any column that isn't passed):

```
DB::table('users')->insert(['username' => 'test_user', 'role' => 'user']);
```

```
DB::table('users')->insert(['id' => 5, 'username' => 'test_user', 'role' => 'user']);
```

- Update row:

```
DB::table('users')->where('id', '=', 5)->update('role', 'user');
```

- Delete row:

```
DB::table('users')->where('id', '=', 5)->delete();
```


### ADVANCED USAGE


Lets say we have a second table named info
```
+---------+-------------+------+-----+---------+----------------+
| Field   | Type        | Null | Key | Default | Extra          |
+---------+-------------+------+-----+---------+----------------+
| id      | int(11)     | NO   | PRI | NULL    | auto_increment |
| user_id | int(11)     | NO   | MUL | NULL    |                |
| address | varchar(25) | NO   |     | NULL    |                |
+---------+-------------+------+-----+---------+----------------+
```
That contains these values 
```
+----+---------+-------------------+
| id | user_id | address           |
+----+---------+-------------------+
|  1 |       1 | Some address 134  |
+----+---------+-------------------+
```

- Perform a join query:

```
DB::table('users')->select('*')->join('info', 'users.id', 'user_id')->get();
```

- Check if a column value is in a set of values:

```
$subquery = DB::table('info')->select('user_id');
DB::table('users')->select('*')->in('id', $subquery)->get()
```

ORM Documentation can be found [here](EasySQL/Src/Entities)