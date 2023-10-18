<?php
include 'check_admin.php';
if(!$isadmin || !isset($_GET['imdb_code'])) header( "Location: ../login.php" );

//Connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

//get movie code
$imdb_code=urlencode($_GET['imdb_code']);

//query
$query="SELECT * from `movies` where `imdb_code`='$imdb_code'";
$conn = mysqli_connect($servername,$username,$password,$dbname);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
$result = mysqli_query($conn,$query);
$conn -> close();
if(mysqli_num_rows($result)==0) $json = array("message" => "Error", "content" => "IMDb code error");
else{
	$movie=$result -> fetch_array(MYSQLI_ASSOC);
	$json = array("message" => "Error", "content" => $movie);
}
print_r(json_encode($json));
