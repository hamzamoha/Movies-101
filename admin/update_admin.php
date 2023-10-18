<?php
include 'check_admin.php';
if(!$isadmin) header("Location: ../login.php");

if($_SERVER['REQUEST_METHOD']=='POST'){
	
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
	
	if(isset($_POST['Username'])){
		$LastName = $_POST['L_name'];
		$FirstName = $_POST['F_name'];
		$Username = $_POST['Username'];
		$Email = $_POST['Email'];
		$query="UPDATE `admin` SET `LastName`='$LastName',`FirstName`='$FirstName',`Email`='$Email',`Username`='$Username'";
		if($conn->query($query)) {
			$array = array("message" => "Success", "details" => "Changes saved successfuly");
			$_SESSION['id']=md5($Username);
			$_SESSION["F_name"]=$FirstName;
			$_SESSION["L_name"]=$LastName;
			$_SESSION["Username"]=$Username;
			$_SESSION["Email"]=$Email;
		}
		else $array = array("message" => "Datbase Error", "details" => "Query or mysql error");
	}
	else if(isset($_POST['Current_password'])){
		$Current_password = $_POST['Current_password'];
		$query="SELECT `Username`,`Password` From `admin`";
		$result = $conn->query($query);
		$admin_info = $result->fetch_assoc();
		if($Current_password != $admin_info["Password"]){
			$array = array("message" => "Error", "details" => "Password is not correct");

		}
		else {
			$New_password = $_POST['New_password'];
			$query="UPDATE `admin` SET `Password`='$New_password'";
			if($conn->query($query)){
				$array = array("message" => "Success", "details" => "Password changed successfuly");
			}
			else $array = array("message" => "Datbase Error", "details" => "Query or mysql error");
		}
	}
	print_r(json_encode($array));
	$conn->close();
}
