<?php

//Declaring variables to prevent errors
$fname = ""; //first name
$lname = ""; //last name
$em = ""; //email
$em2 = ""; //email2
$password = "";
$password2 = "";
$date = "";	//Sign up date
$error_array= array(); //Holds error Messagex

if(isset($_POST['register_button'])) {

	//Registration form values

	//First Name
	$fname = strip_tags($_POST['reg_fname']);//remove html tags
	$fname = str_replace(' ', '', $fname); // uppercase first letter
	$fname = ucfirst(strtolower($fname));
	$_SESSION['reg_fname'] = $fname; // stores first name into session variable

	//Last Name
	$lname = strip_tags($_POST['reg_lname']);
	$lname = str_replace(' ', '', $lname);
	$lname = ucfirst(strtolower($lname));
	$_SESSION['reg_lname'] = $lname;

	//email
	$em = strip_tags($_POST['reg_email']);
	$em = str_replace(' ', '', $em);
	$em = ucfirst(strtolower($em));
	$_SESSION['reg_email'] = $em;

	//email 2
	$em2 = strip_tags($_POST['reg_email2']);
	$em2 = str_replace(' ', '', $em2);
	$em2 = ucfirst(strtolower($em2));
	$_SESSION['reg_email2'] = $em2;

	//Password
	$password = strip_tags($_POST['reg_password']);
	$password2 = strip_tags($_POST['reg_password2']);

	$date = date("Y-m-d"); //current date


	if($em == $em2) {
		//check if email is in valid format
		if(filter_var($em, FILTER_VALIDATE_EMAIL)){

			$em = filter_var($em, FILTER_VALIDATE_EMAIL);

			//check if email already exist
			$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

			//count the number of rows returned
			$num_rows = mysqli_num_rows($e_check);

			if($num_rows > 0) {
				array_push($error_array, "Email already in use<br>");
			}
		}
		else {
			array_push($error_array, "Invalid email format<br>");
		}
	}
	else {
		array_push($error_array, "Emails don't match<br>");
	}


	if(strlen($fname) > 25 || strlen($fname) < 2) {
		array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
	}

	if(strlen($lname) > 25 || strlen($lname) < 2) {
		array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
	}

	if($password != $password2) {
		array_push($error_array, "Your password do not match<br>");
	}
	else {
		if(preg_match('/[^A-Za-z0-9]/', $password)) {
			array_push($error_array, "Your password can only contain english characters or numbers<br>");
		}
	}

	if(strlen($password) > 30 || strlen($password) < 5) {
		array_push($error_array, "Your password must be between 5 and 30 characters<br>");
	}


	if(empty($error_array)) {
		$password = md5($password); //Encrypt password before sending to database

		//Generate username by concatenating first name and last name
		$username = strtolower($fname. " _ " .$lname);
		$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

		$i = 0;
		//if username exist add number to username 
		while(mysqli_num_rows($check_username_query) != 0) {
			$i++;
			$username = $username . "_" . $i;
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		}
	

		//profile picture assignment
		$rand = rand(1, 2);	//random number between 1 and 2

		if($rand == 1)
			$profile_pic = "assets/images/profile_pics/defaults/head_pumpkin.png";
		else
			$profile_pic = "assets/images/profile_pics/defaults/head_turqoise.png";

		$query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname','$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

		array_push($error_array, "<span style='color:#14C800;'>You're all set! Goahead and login!</span><br>");

		//Clear session variables
		$_SESSION['reg_fname'] = "";
		$_SESSION['reg_lname'] = "";
		$_SESSION['reg_email'] = "";
		$_SESSION['reg_email2'] = "";
	}
}
?>
