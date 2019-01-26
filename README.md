# Read Me

EasySQL is a PHP API that allows users to perform simple SQL queries
in a one liner code. EasySQL uses prepared statements to avoid SQL injections.

EasySQL provides a UI with multiple dropdowns. Selecting values from
these dropdowns provides the EasySQL function call for the parameters
passed by the user. The UI is under the EasySQL/UI directory.
This makes the execution of the SQL queries really easy.
At this moment UI does not provide multiple values selection even though the api does.

# Installation

Prerequisites: SQL DBMS (mysql, sqlite etc), php, PDO module.

To install EasySQL you need to install the modules needed from the directory
where composer.json is, by executing:

```
composer install
```

Create a database.

Then execute the build.php file

```
 php build.php
```

complete all the needed information and you are ready to go.

The .env direcotry should be outside of the document root since it contains the
database credentials.

# Usage

The api call is a single liner as such:

(new API())->action(table)->more()

# Documentation

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
That contains these values:
```
+----+----------+------------------+----------+-----------+-------+---------------------+---------------------+
| id | username | mail             | password | is_active | role  | created_at          | updated_at          |
+----+----------+------------------+----------+-----------+-------+---------------------+---------------------+
|  1 | admin    | admin@mysite.com | secret   |         1 | admin | 2018-05-25 00:00:00 | 2018-06-09 12:53:36 |
|  2 | dani     | dani@example.com | terces   |         0 | user  | 2018-05-30 00:00:00 | 2018-06-06 00:00:00 |
+----+----------+------------------+----------+-----------+-------+---------------------+---------------------+
```
Now we want to perform some action using the EasySQL API.

## Actions

- Get
- Update
- Insert
- Delete

## Examples

- The API call to get all the values from the rows would be:

```
(new API())->get('users')->execute();
```

- You can limit or order the results of an API call by using:

```
(new API())->get('users')->options(['limit' => 1])->execute();
(new API())->get('users')->options(['order' => 'username ASC'])->execute();
```

- If you want to combine these two ordering should always preceed limit:

```
(new API())->get('users')->options(['order' => 'username ASC', 'limit' => 1])->execute();
```

- The API call to get all the values from the row with id equal to 1 is:

```
(new API())->get('users')->where(['id' => 1])->execute();
```

- The API provides support for operators such as (>, <, <>, <=, >=, LIKE).
The API call to get all the columns that have id greater than 1 is:

```
(new API())->get('users')->where([['operator' => '>', 'id' => 1]])->execute();
(new API())->get('users')->where([['operator' => 'LIKE', 'username' => '%da%']])->execute();
```

- The API provides support for conditions (AND, OR).
The API call to get the columns that have id 1 or 2 is:

```
(new API())->get('users')->where(['id' => 1, ['id' => 2], 'condition' => ['OR']])->execute();
```

- The API call to get a certain value (in this case username) from the rows is:

```
(new API())->get('users')->return('username')->execute();
```

or if you want to return multiple values:

```
(new API())->get('users')->return('username', 'mail')->execute();
```

- The API call to update a certain row is:

```
(new API())->update('users')->set('username', 'root')->where(['id' => 1])->execute();
```

This will change the admin username to root.

- Now to insert a new row we should see the columns we have to complete.

We see that id and updated_at columns have auto completed values that
means we can just leave them out of the parameters array.

Also the created_at column is nullable that means if you don't want
to keep a record of when the user was created you can skip this in
the parameters array.

The api call to insert a new row is:

```
(new API())->insert('users')->values(['username' => 'george', 'mail' => 'george@example.com', 'password' => 'secret', 'is_active' => 0, 'role' => 'user', 'created_at' => '2018-06-02 00:00:00'])->execute();
```

- The api call to delete the user dani is:

```
(new API())->delete('users')->where(['id' => 2])->execute();
```

- You can also perform a join on two tables

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
We can perform a join as such

```
(new API())->get('users', 'info', 'id', 'user_id')->execute();
(new API())->get('users', 'info', 'id', 'user_id')->return('username', 'info.address')->execute();
```

The call parameters are (new API())->get(table, join, onTable, onJoin)->execute();
