<?php
include './admin/check_admin.php';
$message = "";
if ($isadmin) header("Location: /admin/");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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

	if (isset($_POST['email'])) {
		$email = $_POST['email'];
		$email = explode("+", explode(" ", $email)[0])[0];
		$query = "SELECT * FROM `admin` where `Email`='$email'";
		$result = $conn->query($query);
		$isexist = $result->num_rows;
		if ($isexist) {
			$admin = $result->fetch_array(MYSQLI_ASSOC);
			$code = chr(rand(65, 90)) . chr(rand(65, 90)) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
			$query = "DELETE FROM `hashes` where `username`='" . $admin['Username'] . "'";
			$conn->query($query);
			$query = "INSERT INTO `hashes` (`hash`,`username`) VALUES ('" . md5($code) . "','" . $admin['Username'] . "')";
			$conn->query($query);
			$conn->close();
			$to = "$email";
			$subject = "Reset your password";
			$txt = "<h4>Hello admin " . $admin['FirstName'] . " " . $admin['LastName'] . "</h4>";
			$txt .= "Use this code to reset your password account Movies: $code<br/>";
			$txt .= "<b>DO NOT SHARE THIS CODE !</b>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Robot@localhost' . "\r\n";
			$headers .= 'Cc: Robot@localhost' . "\r\n";
			if (!mail($to, $subject, $txt, $headers)) {
				$array = array("message" => "DB Error", "details" => "Email was not sent corrcetly. try again later");
				print_r(json_encode($array));
				exit();
			} else {
				$array = array(
					"message" => "Success",
					"details" => "
				<label>We've sent a code to your email, check it.</label>
				<input name='code' type='text' placeholder='Enter your code here'>
				<input type='submit' value='Submit'>"
				);
				print_r(json_encode($array));
				exit();
			}
		} else {
			$array = array("message" => "Prompt Error", "details" => "Email is not corrcet");
			print_r(json_encode($array));
			exit();
		}
	} else if (isset($_POST['code'])) {
		$code = md5($_POST['code']);
		$query = "SELECT * FROM `hashes` where `hash`='$code' and `status`='alive'";
		if (!($result = $conn->query($query))) {
			$array = array("message" => "DB Error", "details" => "Query failed: %s\n" . $conn->error);
			print_r(json_encode($array));
			exit();
		}
		$conn->close();
		$info = $result->fetch_array(MYSQLI_ASSOC);
		$isexist = $result->num_rows;
		if ($isexist) {
			$array = array("message" => "Success", "details" => "
	<input type='password' name='new_password' placeholder='New Password' required/>
	<input type='password' name='confirm_password' placeholder='Confirm Password' required/>
	<input type='submit' value='Submit'>
			");
			print_r(json_encode($array));
			if (!isset($_SESSION)) session_start();
			$_SESSION['type'] = "reset_password";
			$_SESSION["username"] = $info['username'];
			$_SESSION["hash"] = $info['hash'];
		} else {
			$array = array("message" => "Prompt Error", "details" => "Code is not corrcet");
			print_r(json_encode($array));
		}

		exit();
	} else if (isset($_POST['new_password'])) {
		if (!isset($_SESSION)) session_start();
		if (!isset($_SESSION["hash"])) {
			$array = array("message" => "Hack Error", "details" => "This shouldn't happen");
			print_r(json_encode($array));
			exit();
		}
		$query = "SELECT * FROM `hashes` where `hash`='" . $_SESSION["hash"] . "' and `status`='alive' and `username`='" . $_SESSION["username"] . "'";
		if (!($result = $conn->query($query))) {
			$array = array("message" => "DB Error", "details" => "Query failed: %s\n" . $conn->error);
			print_r(json_encode($array));
			exit();
		}
		$isexist = $result->num_rows;
		if ($isexist) {
			$info = $result->fetch_array(MYSQLI_ASSOC);
			if ($_POST['new_password'] != $_POST['confirm_password']) {
				$array = array("message" => "Prompt Error", "details" => "Confirm your password !!");
				print_r(json_encode($array));
				exit();
			}
			$password = $_POST['new_password'];
			$username = $info['username'];
			$query = "DELETE FROM `hashes` where `username`='" . $username . "'";
			if (!($conn->query($query))) {
				$array = array("message" => "DB Error", "details" => "Query failed: %s\n" . $conn->error);
				print_r(json_encode($array));
				exit();
			}
			$query = "UPDATE `admin` SET `Password`='$password' where `Username`='$username'";
			if (!($conn->query($query))) {
				$array = array("message" => "DB Error", "details" => "Query failed: %s\n" . $conn->error);
				print_r(json_encode($array));
				exit();
			}
			$query = "SELECT * FROM `admin` where `Username`='$username'";
			if (!($result = $conn->query($query))) {
				$array = array("message" => "DB Error", "details" => "Query failed: %s\n" . $conn->error);
				print_r(json_encode($array));
				exit();
			}
			$admin = $result->fetch_array(MYSQLI_ASSOC);
			unset($_SESSION["hash"]);
			unset($_SESSION["username"]);
			$_SESSION['type'] = "admin";
			$_SESSION['id'] = md5($admin['Username']);
			$_SESSION["F_name"] = $admin['FirstName'];
			$_SESSION["L_name"] = $admin['LastName'];
			$_SESSION["Username"] = $admin['Username'];
			$_SESSION["Email"] = $admin['Email'];
			$conn->close();
			$array = array("message" => "Success", "details" => "<label>Your password has changed successfully</label>
			<input onclick='window.location = document.location.origin + \"/admin\"' type='button' value='Go to admin page'>");
			print_r(json_encode($array));
			exit();
		} else {
			$array = array("message" => "Hack Error", "details" => "This shouldn't happen");
			print_r(json_encode($array));
			exit();
		}
	} else {
		$admin_username = urlencode($_POST['username']);
		$admin_password = urlencode($_POST['password']);
		$query = "SELECT * FROM `admin` where `Username`='$admin_username'";
		$result = mysqli_query($conn, $query);
		$conn->close();
		$isexist = mysqli_num_rows($result);
		if ($isexist == 0) {
			$admin_username = "";
			$message = "Wrong username and password !";
		} else {
			$admin = $result->fetch_array(MYSQLI_ASSOC);
			if ($admin_password == $admin['Password']) {
				if (!isset($_SESSION)) session_start();
				$_SESSION['type'] = "admin";
				$_SESSION['id'] = md5($admin_username);
				$_SESSION["F_name"] = $admin['FirstName'];
				$_SESSION["L_name"] = $admin['LastName'];
				$_SESSION["Username"] = $admin['Username'];
				$_SESSION["Email"] = $admin['Email'];

				header("Location: /admin/");
			} else $message = "Wrong password !";
		}
	}
}
?>
<html>

<head>
	<link rel="stylesheet" href="fonts.css" />
	<link href="/fonts/fontawesome-5.14.0/css/all.css" rel="stylesheet">
	<title>Login</title>
	<style>
		* {
			padding: 0;
			margin: 0;
			border: 0;
			text-decoration: none;
			list-style: none;
			font-family: 'TitilliumWeb';
		}

		main {
			height: 100%;
			width: 100%;
			background: url(imgs/login_wallpaper.jpg) center no-repeat;
			position: relative;
			background-size: cover;
		}

		.login-form {
			width: 50%;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			text-align: center;
		}

		.login-form h2 {
			line-height: 70px;
			background: #f7be10;
			color: #fff;
			text-shadow: 0 0 6px rgba(255, 255, 255, 0.5);
		}

		.login-form form i {
			display: block;
			width: 50px;
			height: 50px;
			line-height: 50px;
			float: left;
			background: #f0f0f0;
			color: #f7be10;
		}

		.login-form input {
			width: calc(100% - 50px);
			display: inline-block;
			text-indent: 5px;
			line-height: 50px;
		}

		.login-form input[type="submit"] {
			text-indent: 0;
			font-weight: 600;
			cursor: pointer;
			color: #f7be10;
			font-size: 16px;
			width: 100%;
			display: block;
			transition: all .4s;
		}

		.login-form input[type="submit"]:hover {
			background: #ddd;
		}

		.forgot_password {
			line-height: 50px;
			background: #fff;
		}

		.forgot_password a {
			line-height: 50px;
			color: #6a6a6a;
		}

		.login_icon i {
			line-height: 90px;
			font-size: 50px;
			background: #f0f0f0;
			color: #f7be10;
			width: 90px;
			border-radius: 50%;
			margin-bottom: 10px;
		}

		.login-form form {
			background: #fff;
		}

		#message {
			position: fixed;
			bottom: 20px;
			width: 100%;
		}

		#message p {
			margin: 0 auto;
			width: 300px;
			text-align: center;
			background: #fff;
			line-height: 40px;
			color: #f15151;
			font-weight: 600;
			border: solid 2px #f15151;
			<?php if ($message == "") echo "display: none;";
			else echo "display: block;" ?>
		}

		.reset_password_form {
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			background: rgba(0, 0, 0, 0.5);
			display: none;
		}

		.reset_password_form form {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 50%;
			text-align: center;
			background: #fff;
			box-sizing: border-box;
			padding: 0 20px;
			display: none;
		}

		.reset_password_form form label {
			display: block;
			line-height: 50px;
		}

		.reset_password_form form input {
			display: block;
			width: 100%;
			line-height: 50px;
			margin-bottom: 10px;
		}

		.reset_password_form form input[type="email"],
		.reset_password_form form input[type="password"],
		.reset_password_form form input[type="text"] {
			text-indent: 5px;
			border-radius: 3px;
			box-shadow: inset 0 0 3px 0 rgba(0, 0, 0, 0.2);
		}

		.reset_password_form i {
			position: absolute;
			top: 5px;
			right: 5px;
			background: #f7be10;
			width: 30px;
			line-height: 30px;
			text-align: center;
			color: #fff;
			font-size: 20px;
			cursor: pointer;
		}
	</style>
	<script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
	<script>
		$(document).ready(function() {
			username = $("input[name=username]");
			if (username.val() == "") $(username).focus();
			else $("input[name=password]").focus();
			<?php if ($message != "") echo 'setTimeout(function(){$("#message p").hide();},2000);'; ?>
		});

		$(function() {
			$(".reset_password_form form").submit(function() {
				$(".reset_password_form form input[type='submit']").attr("disabled", true);
				$(".reset_password_form form input[type='submit']").val("Loading...");
				$.post("?", $(this).serialize(), function(json) {
					message = json.message;
					details = json.details;
				}, "json").done(function() {
					if (message == 'Prompt Error') {
						$('.reset_password_form form').append("<label class='prompt'>" + details + "</label>");
						$(".reset_password_form form input[type='submit']").attr("disabled", false);
						$(".reset_password_form form input[type='submit']").val("Submit");
						setTimeout(function() {
							$(".reset_password_form label.prompt").remove();
						}, 2000);
					} else {
						$('.reset_password_form form').html(details);
					}
				}).fail(function() {
					console.log("Jquery Error: Post Failed");
				});
				return false;
			});
		});
	</script>
</head>

<body>
	<main>
		<div class='login-form'>
			<div class='login_icon'><i class='fas fa-user'></i></div>
			<h2>Admin Panel | Login</h2>
			<form method='post' action="/login.php" autocomplete="off" accept-charset="utf-8">
				<i class="fas fa-user"></i><input name='username' type='text' value='<?php if (isset($admin_username)) echo $admin_username; ?>' placeholder='username' required>
				<i class="fas fa-key"></i><input name='password' type='password' placeholder='password' required>
				<input name='login' type='submit' value='Log in'>
			</form>
			<div class='forgot_password'><a href='#' onclick='$(".reset_password_form").show(); $(".reset_password_form form").slideDown()'>forgot password?</a></div>
		</div>
		<div class="reset_password_form">
			<form method='post' action='/login.php' autocomplete="off" accept-charset="utf-8">
				<label>Enter your email: </label>
				<input type="email" placeholder="Email" name="email" required>
				<input type="submit" name='go' value="Send Verefication code">
			</form>
			<i class='fas fa-times' onclick='$(".reset_password_form").hide(); $(".reset_password_form form").hide()'></i>
		</div>
		<div id='message'>
			<p><?php echo $message; ?></p>
		</div>
	</main>
</body>

</html>