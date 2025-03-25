<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


    // Database configuration
    $host = 'localhost';
    $dbname = 'orgman';
    $username = 'root';
    $password = '';

    $conn = mysqli_connect($host, $username ,$password, $dbname);
    