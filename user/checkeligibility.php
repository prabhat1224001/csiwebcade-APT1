<?php

// To Handle Session Variables on This Page
session_start();

if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

// If user actually clicked apply button
if (isset($_GET)) {

    // Get user details
    $sql = "SELECT * FROM users WHERE id_user='$_SESSION[id_user]'";
    $result1 = $conn->query($sql);

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();

        // Ensure that the values are numeric; default to 0 if they are not
        $hsc = isset($row1['hsc']) && is_numeric($row1['hsc']) ? (float)$row1['hsc'] : 0;
        $ssc = isset($row1['ssc']) && is_numeric($row1['ssc']) ? (float)$row1['ssc'] : 0;
        $ug = isset($row1['ug']) && is_numeric($row1['ug']) ? (float)$row1['ug'] : 0;
        $pg = isset($row1['pg']) && is_numeric($row1['pg']) ? (float)$row1['pg'] : 0;

        // Calculate total percentage
        $sum = $hsc + $ssc + $ug + $pg;
        $total = $sum / 4;

        $course1 = $row1['qualification'];
    }

    // Get job post details
    $sql = "SELECT maximumsalary, qualification FROM job_post WHERE id_jobpost='$_GET[id]'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Ensure the eligibility value is numeric
        $eligibility = is_numeric($row['maximumsalary']) ? (float)$row['maximumsalary'] : 0;
        $course2 = $row['qualification'];

        // Check eligibility
        if ($total >= $eligibility) {
            if ($course1 == $course2) {
                // Eligible for the drive
                header("Location: ../view-job-post.php?id=$_GET[id]");
                $_SESSION['status'] = "You are eligible for this drive, apply if you are interested.";
                $_SESSION['status_code'] = "success";
                exit();
            } else {
                // Not eligible due to course mismatch
                header("Location: ../view-job-post.php?id=$_GET[id]");
                $_SESSION['status'] = "You are not eligible for this drive due to the course criteria. Check out other drives.";
                $_SESSION['status_code'] = "success";
                exit();
            }
        } else {
            // Not eligible due to percentage or course criteria
            header("Location: ../view-job-post.php?id=$_GET[id]");
            $_SESSION['status'] = "You are not eligible for this drive either due to the overall percentage criteria or course criteria. Update your marks in your profile if you think you are eligible.";
            $_SESSION['status_code'] = "success";
            exit();
        }
    }
}
?>
