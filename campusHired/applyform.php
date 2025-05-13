<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
?>


<?php

include "dblocal.php";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} 
catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}


if (isset($_GET['id'])) {

    $jobid = $_GET['id'];
    
    
    $stmt = $conn->prepare("SELECT * FROM job WHERE jobid = :jobid");
    $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo "No record found such id";
        $result = []; // 如果没有结果，设置为空数组，避免后续访问时报错
    }

    $employerid = $result['employerid'];
    $stmt = $conn->prepare("SELECT * FROM employer WHERE userid = :employerid");
    $stmt->bindParam(':employerid', $employerid, PDO::PARAM_INT);
    $stmt->execute();
    $employer = $stmt->fetch(PDO::FETCH_ASSOC);


    $studentid = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM student WHERE userid = :studentid");
    $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);


}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    $stmt = $conn->prepare("SELECT * FROM application WHERE studentid = :studentid");
    $studentid = $_POST['student_id'];
    $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
    $stmt->execute();
    $we = $stmt->fetch(PDO::FETCH_ASSOC);
    //echo $we['studentid'];
    //die();

    if(!$we){
        $stmt = $conn->prepare("INSERT INTO application 
            (jobid, studentid, employerid, student, student_email, student_matric, student_faculty, employer, employer_email, shop, position, status, jobtitle) 
            VALUES 
            (:jobid, :studentid, :employerid, :student, :student_email, :student_matric, :student_faculty, :employer, :employer_email, :shop, :position, :status, :jobtitle)");

        $jobid = $_POST['jobid'];
    //echo $jobid;
    //die();
        $studentid = $_POST['student_id'];
        $employerid = $_POST['employer_id'];
        $student = $_POST['student'];
        $student_email = $_POST['student_email'];
        $student_matric = $_POST['student_matric'];
        $student_faculty = $_POST['student_faculty'];
        $employer = $_POST['employer'];
        $employer_email = $_POST['employer_email'];
        $shop = $_POST['shop'];
        $position = $_POST['position'];
        $status = $_POST['status'];
        $jobtitle = $_POST['jobtitle'];

        $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
        $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
        $stmt->bindParam(':employerid', $employerid, PDO::PARAM_INT);
        $stmt->bindParam(':student', $student, PDO::PARAM_STR);
        $stmt->bindParam(':student_email', $student_email, PDO::PARAM_STR);
        $stmt->bindParam(':student_matric', $student_matric, PDO::PARAM_STR);
        $stmt->bindParam(':student_faculty', $student_faculty, PDO::PARAM_STR);
        $stmt->bindParam(':employer', $employer, PDO::PARAM_STR);
        $stmt->bindParam(':employer_email', $employer_email, PDO::PARAM_STR);
        $stmt->bindParam(':shop', $shop, PDO::PARAM_STR);
        $stmt->bindParam(':position', $position, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':jobtitle', $jobtitle, PDO::PARAM_STR);

        try {
            $stmt->execute();
            header("Location: homes.php");
            exit;
            echo "Application submitted successfully!";
        } catch (PDOException $e) {
    // 如果出现错误，输出详细的错误信息
            echo "Error: " . $e->getMessage();
        }

    }

    else {
        $message = urlencode("You cannot send more applications if you already have one!");
        header("Location: homes.php?message=$message");
        exit;
    }





    

    


}



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Frank+Ruhl+Libre&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Apply for Job</title>
    <link rel="stylesheet" href="applyform_style.css">
    <style>


    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h1>CampusHired</h1>
            <nav>
                <ul>

                    <li><a href="homes.php">
                       <ion-icon name="bag-outline"></ion-icon>Go For Jobs
                   </a>
               </li>

               <li>
                 <a href="profiles.php">
                  <ion-icon name="person-outline"></ion-icon>Profile
              </a>
          </li>

          <li>
            <a href="status.php">
              <ion-icon name="document-text-outline"></ion-icon>My Application
          </a>
      </li>

      <li>
    <a href="chats.html">
      <ion-icon name="chatbubbles-outline"></ion-icon> Messages
  </a>
</li>



      <li>
        <a href="logout.php">
           <ion-icon name="log-out-outline"></ion-icon>Log out
       </a>
   </li>
</ul>
</nav>
</aside>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Box: Logo, Job Title, Apply Button -->
    <div class="top-box">
        <div id="logo-preview-container" style="margin-top: 10px;">
            <?php 
                        // Convert binary image data to Base64
            $base64Image = 'data:' . $result['mime_type'] . ';base64,' . base64_encode($result['pic_data']); 
            ?>
            <img id="logo-preview" src="<?php echo $base64Image; ?>" alt="Logo Preview" style="max-width: 80px; max-height: 80px; display: block; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
        </div>

        
        <div class="logo">
            <?php echo $result['shop'];?>
            (Job ID:<?php echo $result['jobid'];?>)
        </div>
        <form id="hiddenForm" action="applyform.php" method="POST" style="display: none;">

            <input type="hidden" name="jobid" value="<?php echo $result['jobid'];?>">

            <input type="hidden" name="student_id" value="<?php echo $student['userid']; ?>">
            <input type="hidden" name="employer_id" value="<?php echo $employer['userid']; ?>">
            <input type="hidden" name="student" value="<?php echo $student['firstname'];?>">
            <input type="hidden" name="student_email" value="<?php echo $student['email'];?>">
            <input type="hidden" name="student_matric" value="<?php echo $student['matric'];?>">
            <input type="hidden" name="student_faculty" value="<?php echo $student['faculty'];?>">

            <input type="hidden" name="employer" value="<?php echo $employer['firstname'];?>">
            <input type="hidden" name="employer_email" value="<?php echo $employer['email'];?>">
            <input type="hidden" name="shop" value="<?php echo $result['shop'];?>">
            <input type="hidden" name="position" value="<?php echo $result['location'];?>">
            <input type="hidden" name="status" value="Applied">
            <input type="hidden" name="jobtitle" value="<?php echo $result['jobtitle'];?>">
        </form>
        <a href="#" class="apply-btn" onclick="submitForm()">Apply</a>

        <script>
            function submitForm() {
    // 获取表单元素
                const form = document.getElementById('hiddenForm');
    // 提交表单
                form.submit();
            }
        </script>
    </div>

    <!-- Job Description and Info -->
    <div class="details-container">
        <!-- Job Description Box -->
        <div class="job-description">
            <div class="heading">About the job</div>
            <div class="content">
                <?php echo $result['des'];?>
                <br><br>
                <strong>Responsibilities:</strong>
                <ul>
                    <li>Employer: <?php echo $result['employer'];?> (ID: <?php echo $result['employerid'];?>)</li>
                    <li>Student: <?php echo $student['firstname'];?>, Matric No. <?php echo $student['matric'];?> (ID: <?php echo $student['userid'];?>)</li>
                    <li>Student Faculty: <?php echo $student['faculty'];?></li>
                </ul>
            </div>
        </div>

        <!-- Job Info Box -->
        <div class="job-info">
            <div class="info-heading">Job Information</div>

            <div class="info-content">
             <ion-icon name="time-outline"></ion-icon> <span>Working Time:</span> 
             <div class="value">
                <?php
                echo substr($result['starttime'], 0, 5) . ' - ' . substr($result['endtime'], 0, 5);
                ?>
            </div>

            <div class="info-content">
                <ion-icon name="cash-outline"></ion-icon> <span>Salary:</span> 
                <div class="value">RM<?php echo $result['salary'];?> per hour
                </div>

                <div class="info-content">
                    <ion-icon name="location-outline"></ion-icon> <span>Location:</span> 
                    <div class="value"><?php echo $result['location'];?>
                </div>

                <div class="info-content">
                    <ion-icon name="briefcase-outline"></ion-icon> <span>Job Type:</span> 
                    <div class="value"><?php echo $result['jobtype'];?>
                </div>
            </div>

        </div>
    </main>
</div>
</body>
</html>
