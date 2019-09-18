# ORM Usage

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

Create a class that extends the Entity class that class must have the same name as the table

```
<?php
	use EasySQL\Entities\Entity;

	class Users extends Entity {}
```

### BASIC USAGE

An entity object can perform all the queries the Builder can by changing the DB::table('users') to $users

So here we will focus on the unique methods of an Entity object

- Create a new object

```
$users = new Users();
```

- Get all the columns of the table:

```
$users->all();
```

- Filter the values of the rows:

```
$users->filterById(3)->all();

$users->filterById('>', 1)->and()->filterByUsername(dani')->all();

$user->limit(5)->not()->between('id', 2, 28)->all();

// Subquery
$sub = (new Users)->select('id')->filterById('dani');

$users->exists($sub)->all();

$users->filterByUsername('admin')->or()->between('id', 2, $sub)->all();

$users->filterById(2)->or()->filterById(4)->order('id', 'DESC')->all();

$users->in('id', [9, 13, 35])->or()->in('id', [2, 3])->all();

$users->in('id', [9, 13, 35])->or()->in('id', $sub)->all();


```

- Insert new row (will automatically set null any column that isn't passed):

Create a new entity object, complete its properites and call the save method

```
$users = new Users();
$users->username = 'test';
$users->email = 'test@test.com';
$users->password = 'secret';
$users->is_active = 0;
$users->role = 'user';
$users->save();
```

- Update row:

The same process with insert but with an object id that already exists in the database

```
// Admin username
$user = $users->filterById(1)->all();
$user->username = 'galactic_lord';
$user->save();
```

- Delete row:

```
$user = $users->filterById(1)->all();
$user->delete();
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

- Perform a join query (one to one relationship):

```
$users->join('info', 'users.id', 'user_id')->all();
```

- Or use the relationship methods

```
$users->ownerOfOne('info');
```

- The other relationship method are:

```
memberOfOne //inverse of one to one
ownerOfMany // one to many
memberOfMany //inverse of one to many
membersHaveMany // many to many
```

- Perform a union query:

```
$users->filterById('>', 9)->union($sub)->all()
```

API Documentation can be found [here](../../../)