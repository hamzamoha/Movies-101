<?php
session_start();
if(!isset($_SESSION['type'])) {$isadmin = 0; goto endcode;}
//Connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

//query
$query="SELECT `Username`,`Password` FROM `admin` where 1";

//Connect to db
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$array = array("message" => "DB Error", "details" => "Error while connecting to db.");
	print_r(json_encode($array));
	exit();
}

//get admins
$result = mysqli_query($conn, $query);
$conn->close();
while($row = $result -> fetch_array(MYSQLI_ASSOC)){
	if($_SESSION['type']=="admin" && $_SESSION['id']==md5($row["Username"])){
		$isadmin = 1;
		break;
	}
	else $isadmin = 0;
}
endcode:
