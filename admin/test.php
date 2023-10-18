<?php
include 'check_admin.php';
if(!$isadmin) header( "Location: ../login.php" );
$imgs_folder = "../imgs/movies posters/";
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

//Connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

//get movie info
$imdb_code=$_POST['imdb_code'];
$title=$_POST['title'];
$year=$_POST['year'];
$rating=$_POST['rating'];
$genres=$_POST['genres'];
$poster=$_POST['poster'];
$summary=addslashes($_POST['summary']);
$language=$_POST['language'];
$url=$_POST['url'];
$quality=$_POST['quality'];

//check img extension, upload img
$img = basename($poster);
$start = strrpos($img, '.')+1;
$length =strlen($img);
$img=substr($img, $start, $length-$start);
if($img != 'jpg' && $img != 'jpeg' && $img != 'png') {
	$array = array("message" => "Poster error", "details" => "Poster format is not supported");
	print_r(json_encode($array));
	exit();
}
$img=$imdb_code.".".$img;
file_put_contents($imgs_folder.$img,file_get_contents($poster));

//Connect to db
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$array = array("message" => "DB Error", "details" => "Error while connecting to db.");
	print_r(json_encode($array));
	exit();
}

//Check if movie exist
$query = "SELECT `movies`.`imdb_code` FROM `movies` WHERE `movies`.`imdb_code` = '$imdb_code'";
$result = mysqli_query($conn, $query); 
if (mysqli_num_rows($result) > 0) {
	$conn->close();
	$array = array("message" => "Movie error", "details" => "Movie is already exist");
	print_r(json_encode($array));
	exit();
}
//query to add movie
$query = "INSERT INTO `movies` (`imdb_code`, `title`, `year`, `rating`, `genres`, `poster`, `summary`, `language`, `movie_url`, `quality`) VALUES ('$imdb_code', \"$title\", $year, $rating, '$genres', '$img', \"$summary\", '$language', '$url', $quality)";

//add the movie and make sure there's no error
if($conn->query($query) === TRUE) $array = array("message" => "Success", "details" => "Movie has been added successfully");
else $array = array("message" => "Datbase Error", "details" => "Movie was not added: query or mysql error<br>code: ".$conn -> errno);

//exit
$conn->close();

print_r(json_encode($array));
exit();
}
$array = array("message" => "Error", "details" => "No data to handel");
print_r(json_encode($array));
exit();
