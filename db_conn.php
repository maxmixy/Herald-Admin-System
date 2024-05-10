<?php

$servername = "localhost";
	$username = "heraldmember";
	$password = "ctkXb2cVpsQjbjR";
	$dbname = "i8258503_wp1";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	} 