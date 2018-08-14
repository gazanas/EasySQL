# Read Me

EasySQL is a PHP API that allows users to perform simple SQL queries
<<<<<<< HEAD
in a one liner code. EasySQL uses prepared statements to avoid SQL injections.
=======
in a one liner code.
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

EasySQL provides a UI with multiple dropdowns. Selecting values from
these dropdowns provides the EasySQL function call for the parameters
passed by the user. This makes the execution of the SQL queries really easy.
<<<<<<< HEAD
At this moment UI does not provide multiple values selection even though the api does.

# Installation

Prerequisites: SQL DBMS (mysql, sqlite etc), php, PDO module.
=======

# Installation

Prerequisites: SQL DBMS (mysql, sqlite etc), apache2, php, PDO module.
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

To install EasySQL you need to install the modules needed from the directory
where composer.json is, by executing:

composer install

Create a database.

After that you should create the XML Schemata for the tables you want to create.
There are two test xml schemata files as examples in the Schemata directory.

Then go to the build folder, execute the build.php file as: php build.php
complete all the needed information and you are ready to go.

<<<<<<< HEAD
The .env direcotry should be outside of the document root since it contains the
database credentials.

=======
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
# Clean

If you want to clean everything and rebuild the you should run php clean.php from
the build directory. This will remove all the tables and records in the database,
all the DAO files for your table objects and all the database crendentials saved.

# Usage

The api call is a single liner as such:

easy_sql('table name', 'action', array(parameters));

# Documentation

Let's say we have this table named test_users
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
- Value
- Update
- Insert
- Delete

## Examples

- The API call to get all the values from the rows would be:

```
easy_sql('test_users', 'GET', array());
```

- The API call to get all the values from the row with id equal to 1 is:

```
easy_sql('test_users', 'GET', array('id' => 1));
```

- The API call to get all the columns that have id greater than 1 is:

```
easy_sql('test_users', 'GET', array(array('operator' => '>', 'id' => 1)));
```

- The API call to get the columns that have id 1 or 2 is:

```
easy_sql('test_users', 'GET', array('id' => 1, array('id' => 2), 'condition' => array('OR')));
```

- The API call to get a certain value (in this case username) from the rows is:

```
easy_sql('test_users', 'VALUE', array('return' => 'username'));
```

<<<<<<< HEAD
or if you want to return multiple values:

```
easy_sql('test_users', 'VALUE', array('return' => array('username', 'password')));
```

=======
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
- The API call to update a certain row is:

```
easy_sql('test_users', 'UPDATE', array('to_update' => 'username', 'updated' => 'root', 'id' => 1));
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
easy_sql('test_users', 'INSERT', array('username' => 'george', 'mail' => 'george@example.com', 'password' => 'secret', 'is_active' => 0, 'role' => 'user', 'created_at' => '2018-06-02 00:00:00'));
```

- The api call to delete the user dani is:

```
easy_sql('test_users', 'DELETE', array('id' => 2));
```