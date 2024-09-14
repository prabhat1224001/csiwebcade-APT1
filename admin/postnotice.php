<?php

// To Handle Session Variables on This Page
session_start();

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

if (isset($_POST['submit'])) {

    // They take values using name attribute 
    $subject = $_POST['subject'];
    $notice = $_POST['input'];
    $audience = $_POST['audience'];

    // Folder where you want to save your resume. THIS FOLDER MUST BE CREATED BEFORE TRYING
    $folder_dir = "../uploads/resume/";

    // Checking if a file was uploaded
    if (isset($_FILES['resume']) && file_exists($_FILES['resume']['tmp_name'])) {
        
        // Getting Basename of file. So if your file location is Documents/New Folder/myResume.pdf then base name will return myResume.pdf
        $base = basename($_FILES['resume']['name']);

        // Getting the extension of the file
        $resumeFileType = pathinfo($base, PATHINFO_EXTENSION);

        // Setting a random non-repeatable file name
        $file = uniqid() . "." . $resumeFileType;

        // This is where your files will be saved
        $filename = $folder_dir . $file;

        // Moving the uploaded file to the destination folder
        move_uploaded_file($_FILES["resume"]["tmp_name"], $filename);
    } else {
        // If no file is uploaded, set the file variable as an empty string
        $file = '';
    }

    // SQL query to insert data into the notice table
    $sql = "INSERT INTO notice(subject, notice, audience, resume, `date`) 
            VALUES ('$subject', '$notice', '$audience', '$file', now())";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        include 'sendmail.php';
        header("Location: postnotice.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Placement Portal</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="css/AdminLTE.min.css">
    <link rel="stylesheet" href="css/_all-skins.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-green sidebar-mini">
    <?php include 'header.php'; ?>

    <div class="row">
        <div class="col-xs-6 responsive">
            <section>
                <div class="alert alert-success alert-dismissible" style="display: none;" id="truemsg">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    New Notice Successfully added
                </div>

                <form class="centre" action="" method="POST" enctype="multipart/form-data">
                    <div>
                        <h4><strong>Post a new notice</strong></h4>
                    </div>
                    <div>
                        <input id="subject" placeholder="Subject" type="text" name="subject" style="margin:auto">
                    </div>
                    <div id="file" class="form-group">
                        <style>
                            #file {
                                margin-left: 40px;
                                margin-top: 20px;
                            }
                        </style>
                        <input type="file" name="resume" class="btn btn-flat btn-primary">
                    </div>
                    <br>
                    <div class="form-group mt-3">
                        <textarea style="top:80px" class="input" name="input" id="input" placeholder="Notice" required></textarea>
                    </div>
                    <div class="form-group text-center option">
                        <label>Audience</label>
                        <select class="form-control select2 select2-hidden-accessible" style="width: 100%" name="audience">
                            <option value="All Students">All Students</option>
                            <option value="Co-ordinators">Co-ordinators</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary btn-sm" id="submit" name="submit" type="submit">NOTIFY</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="col-xs-5 responsive2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Posted Notice</h3>
                </div>
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Notice</th>
                                <th>Audience</th>
                                <th>File</th>
                                <th>Date and Time</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM notice";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <td><?php echo $row['subject']; ?></td>
                                        <td><?php echo $row['notice']; ?></td>
                                        <td><?php echo $row['audience']; ?></td>
                                        <td>
                                            <?php if ($row['resume'] != '') { ?>
                                                <a href="../uploads/resume/<?php echo $row['resume']; ?>" download="Notice">
                                                    <i class="fa fa-file"></i>
                                                </a>
                                            <?php } else { ?>
                                                No Resume Uploaded
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td><a id="delete" href="deletenotice.php?id=<?php echo $row['id']; ?>"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="main-footer" style="margin:auto; margin-bottom:0px; padding:15px; width:100%; height:50px; position:absolute; background-color:#1f0a0a; color:white">
        <div class="text-center">
            <strong>Copyright &copy; 2022 Placement Portal</strong> All rights reserved.
        </div>
    </footer>

</body>

</html>

<style>
    body {
        background-color: white;
    }
    .centre {
        margin: 20px 30px 100px 20px;
        text-align: center;
        height: 450px;
        width: 700px;
        border: 2px solid black;
        border-radius: 10px;
        display: inline-block;
    }
    #subject {
        width: 86%;
    }
    .option {
        width: 30%;
        margin: auto;
    }
    .input {
        height: 200px;
        width: 600px;
        border-radius: 5px;
        background-color: white;
        text-align: center;
    }
    .button {
        background-color: #3e79c8;
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 0px 10px 0px 10px;
    }
    @media screen and (max-width: 1447px) {
        .centre {
            height: 105%;
            width: 105%;
            margin-left: 100px;
        }
        .responsive2 {
            margin: auto;
            display: block;
            height: 80%;
            width: 80%;
        }
        .input {
            height: 80%;
            width: 60%;
            margin: auto;
        }
    }
</style>
