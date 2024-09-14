<?php


session_start();

if (empty($_SESSION['id_company'])) {
	header("Location: ../index.php");
	exit();
}

//Including Database Connection by garvita
require_once("../db.php");

//if user Actually clicked Add Post Button
if (isset($_POST)) {



	//gargi code section



	$stmt = $conn->prepare("INSERT INTO job_post(id_company, jobtitle, description, minimumsalary, maximumsalary, experience, qualification) VALUES (?,?, ?, ?, ?, ?, ?)");

	$stmt->bind_param("issssss", $_SESSION['id_company'], $jobtitle, $description, $minimumsalary, $maximumsalary, $experience, $qualification);

	$jobtitle = mysqli_real_escape_string($conn, $_POST['jobtitle']);
	$description = mysqli_real_escape_string($conn, $_POST['description']);
	$minimumsalary = mysqli_real_escape_string($conn, $_POST['minimumsalary']);
	$maximumsalary = mysqli_real_escape_string($conn, $_POST['maximumsalary']);
	$experience = mysqli_real_escape_string($conn, $_POST['experience']);
	$qualification = mysqli_real_escape_string($conn, $_POST['qualification']);


	if ($stmt->execute()) {
		//If data Inserted successfully then redirect to dashboard
		$_SESSION['jobPostSuccess'] = true;
		include 'sendmail.php';
		header("Location: index.php");
		exit();
	} else {
		echo "Error ";
	}

	$stmt->close();

	$conn->close();
} else {

	header("Location: create-job-post.php");
	exit();
}
