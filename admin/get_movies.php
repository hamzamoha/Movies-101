<?php

//Set all variables
$limit = 10;
if (isset($_GET["title"])) {
	$title = $_GET["title"];
	$title = base64_decode($title);
} else $title = "";
if (isset($_GET["page"])) {
	$page = intval($_GET["page"]);
	if ($page <= 0) $page = 1;
} else $page = 1;
if (isset($_GET["genre"])) {
	$genre = $_GET["genre"];
} else $genre = "";
if (isset($_GET["year"])) {
	$year = intval($_GET["year"]);
} else $year = "";
if (isset($_GET["order_by"])) {
	$order_by = strtoupper($_GET["order_by"]);
	if ($order_by != "ASC" && $order_by != "DESC") $order_by = "DESC";
} else $order_by = "DESC";
if (isset($_GET["sort_by"])) {
	$sort_by = $_GET["sort_by"];
	if ($sort_by != "title" && $sort_by != "year" && $sort_by != "rating") $sort_by = "year";
} else $sort_by = "year";
$star_index = ($page - 1) * $limit;

//Generate the query
$query = "SELECT * FROM `movies` WHERE `year` LIKE '%$year%' AND `genres` LIKE '%$genre%' AND `title` LIKE '%$title%' ORDER BY `$sort_by` $order_by LIMIT $star_index, $limit";

//Connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

//Connect to db
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$array = array("message" => "DB Error", "details" => "Error while connecting to db.");
	print_r(json_encode($array));
	exit();
}

//Getting the movies
$movies = array();
$result = mysqli_query($conn, $query);
$movies_count = mysqli_num_rows($result);
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	array_push($movies, $row);
}
$json = array("message" => "success", "movies_count" => "$movies_count", "movies" => $movies);
print_r(json_encode($json));

//exit
$conn->close();
