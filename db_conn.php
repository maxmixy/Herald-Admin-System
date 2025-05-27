<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


    // Database configuration
    $host = 'localhost';
    $dbname = 'u659680966_orgman';
    $username = 'u659680966_taskorgman';
    $password = 'Taskorgmanadmin123';

    $conn = mysqli_connect($host, $username ,$password, $dbname);
    