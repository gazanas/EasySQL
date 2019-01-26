<?php

$dbms = readline('Database System (Default mysql): ');
if (isset($dbms) === false || empty($dbms) === true) {
    $dbms = 'mysql';
}

$host = readline('Database Host (Default localhost): ');
if (isset($host) === false || empty($host) === true) {
    $host = 'localhost';
}

while (isset($username) === false || empty($username) === true) {
    $username = readline('Database User: ');
    if (isset($username) === false || empty($username) === true) {
        echo 'Sorry can\'t give empty database user'."\xA";
    }
}


while (isset($database) === false || empty($database) === true) {
    $database = readline('Database Name: ');
    if (isset($database) === false || empty($database) === true) {
        echo 'Sorry can\'t give empty database name'."\xA";
    }
}

$password = readline('Database Password: ');

try {
    new PDO($dbms.':host='.$host.';dbname='.$database, $username, $password);
} catch(PDOException $e) {
    print('Could not establish a connection to the database');
}

$config = 'dbms => '.$dbms."\nhost => ".$host."\nusername => "
.$username."\ndatabase => ".$database."\npassword => ".$password."\n";

file_put_contents('.env/database/config.ini', $config);

shell_exec('composer dump-autoload -o');
