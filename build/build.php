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

while (isset($password) === false || empty($password) === true) {
    $password = readline('Database Password: ');
    if (isset($password) === false || empty($password) === true) {
        echo 'Sorry can\'t give empty password'."\xA";
    }
}

echo shell_exec('php init.php '.$dbms.' '.$host.' '.$username.' '.$database.' '.$password);

$config = 'dbms => '.$dbms."\nhost => ".$host."\nusername => "
.$username."\ndatabase => ".$database."\npassword => ".$password."\n";

file_put_contents(dirname(__DIR__).'/.env/database/config.ini', $config);

chdir(dirname(__DIR__));
shell_exec('composer dump-autoload -o');
