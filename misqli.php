
<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$bdname = "api_d";

	// Creating the connection
	$conn = new mysqli($servername, $username, $password, $bdname);

	// Check the connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	echo "Connected successfully";
?> 
