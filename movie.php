<?php
if (isset($_GET["imdb_code"])) $imdb_code = urlencode($_GET["imdb_code"]);
else header("location: /");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

//query
$query = "SELECT * FROM `movies` where `imdb_code`='$imdb_code'";

//Connect to db
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$array = array("message" => "DB Error", "details" => "Error while connecting to db.");
	print_r(json_encode($array));
	exit();
}

//get results
$result = mysqli_query($conn, $query);
$conn->close();

//handel data
if (mysqli_num_rows($result) == 0) header("location: /");
$movie = $result->fetch_array(MYSQLI_ASSOC);
$title = $movie['title'];
$poster = $movie['poster'];
$summary = $movie['summary'];
$rating = $movie['rating'];
$year = $movie['year'];
$language = $movie['language'];
$genres = $movie['genres'];
$url = $movie['movie_url'];
$quality = $movie['quality'];
//
$title = str_replace("'", "", $title);
?>
<html>

<head>
	<link rel="stylesheet" href="fonts.css" />
	<link rel="stylesheet" href="css/loading.css" />
	<link rel="stylesheet" href="kwg/kwg-video-player.min.css">
	<script src="kwg/kwg-video-player.min.js"></script>
	<style>
		* {
			padding: 0;
			margin: 0;
			border: 0;
			text-decoration: none;
			list-style: none;
			font-family: 'TitilliumWeb';
		}

		body {
			background: #0d1e28;
		}

		#wrapper {
			width: 1200px;
			margin: 0 auto;
			background: #f3f3f3;
			max-width: 100%;
		}

		header nav ul li {
			float: left;
		}

		header nav ul li a {
			display: block;
			padding: 0 10px;
			line-height: 40px;
			color: #FFF;
		}

		header nav ul {
			background: rgba(0, 0, 0, 0.65);
		}

		header nav ul:after {
			content: '';
			display: block;
			clear: both;
		}

		header nav ul li a:hover {
			color: #444;
			background: #f7be10;
			transition: .4s;
		}

		.poster img {
			width: 100%;
		}

		.poster {
			width: 200px;
			float: left;
			box-sizing: border-box;
			padding: 5px;
			background: #f7be10;
			border-radius: 5px;
			height: max-content;
		}

		.movie_body {
			padding: 5px;
			box-sizing: border-box;
		}

		.movie_body:after {
			content: '';
			display: block;
			clear: both;
		}

		.movie_details {
			width: calc(100% - 200px);
			float: left;
			box-sizing: border-box;
			padding: 5px 15px;
			line-height: 32px;
			font-size: 18px;
		}

		.movie_details label {
			font-weight: 600;
		}

		.movie_footer {
			padding: 10px 0;
			text-align: center;
			width: 100%;
			height: 480px;
			margin: 0 auto;
		}

		.movie_footer video {
			width: 100%;
			height: auto;
		}

		.movie_error {
			display: inline-block;
			line-height: 40px;
			font-weight: 600;
			background: #fff;
			border-radius: 5px;
			color: #ff4949;
			box-shadow: 0 0 3px 0;
			padding: 0 15px;
		}

		.summary p {
			text-indent: 15px;
		}
	</style>
	<title><?php
			echo "$title | Watch Online | Movies";
			?></title>
</head>

<body>
	<div id="wrapper">
		<header>
			<nav>
				<ul>
					<li><a href="javascript:void(0)" onclick='window.location = document.location.origin'>Home</a></li>
					<li><a href="#">About us</a></li>
					<li><a href="javascript:void(0)" onclick='window.location = document.location.origin + "/login.php"' href="#">Log in</a></li>
				</ul>
			</nav>
		</header>
		<main>
			<div class='movie_body'>
				<?php echo "
				<div class='poster'><img src='imgs/movies posters/$poster' title='$title' alt='$title'/></div>
				<div class='movie_details'>
					<ul>
						<li><label>Title: </label><span>$title</span></li>
						<li><label>Rating: </label><span>$rating</span></li>
						<li><label>Year: </label><span>$year</span></li>
						<li><label>Language: </label><span>$language</span></li>
						<li><label>Genres: </label><span>$genres</span></li>
						<li><label>Quality: </label><span>$quality</span></li>
					</ul>
					<div class='summary'>
						<label>Story: </label>
						<p>$summary</p>
					</div>
				</div>
";?>
			</div>
			<div class='movie_footer'>
				<?php
				function checkExternalVideo($url)
				{
					//External file
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_NOBODY, true);
					curl_exec($ch);
					$retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					$mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
					$mime = explode("/", $mime)[0];
					curl_close($ch);
					return $mime == 'video' && $retCode == 200;
				}
				function isVideo($url)
				{
					//Internal file
					return file_exists($url) && explode("/", mime_content_type($url))[0] == 'video';
				}
				if (checkExternalVideo($url) || isVideo($url)) {
					echo "<video id='video' controls><source src='$url'>Your browser does not support HTML5 video.</video><script>var video1 = new kwgVideo('#video');</script>";
				} else echo "<iframe width='100%' height='100%' id='gdriveplayer' frameborder='0' allowfullscreen='true' webkitallowfullscreen='true' mozallowfullscreen='true' scrolling='no' src='https://database.gdriveplayer.us/player.php?imdb=$imdb_code' data-src='https://database.gdriveplayer.us/player.php?imdb=$imdb_code'>";
				?>
			</div>
		</main>
		<footer>

		</footer>
	</div>
</body>

</html>