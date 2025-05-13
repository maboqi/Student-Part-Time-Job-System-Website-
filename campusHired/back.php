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


    $user_id = $_SESSION['user_id'];

    // application table 
    $stmt = $conn->prepare("SELECT * FROM application WHERE employerid = :employerid");
    $stmt->bindParam(':employerid', $user_id, PDO::PARAM_INT);
    $stmt->execute();



} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 获取传递的应用 ID 和状态
    $application_id = 1;
        //$application_id = $_POST['application_id'];
    $status = "Accepted";
        //$status = $_POST['status'];

        // 准备 SQL 更新语句
    $stmt = $conn->prepare("UPDATE application SET status = :status WHERE applyid = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $application_id);

        // 执行更新操作
    if ($stmt->execute()) {
        echo "Application status updated successfully!";
            // 可以在这里重定向回主页或其他页面
        header('Location: applist.php');
        exit();
    } else {
        echo "Error updating status.";
    }
}


$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <title>CampusHired - Applications</title>
    <link rel="stylesheet" href="applist_style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h1>CampusHired</h1>

            <ul>

                <li><a href="homee.php">
                   <ion-icon name="home-outline"></ion-icon>Dashboard
               </a>
           </li>

           <li>
             <a href="profilee.php">
              <ion-icon name="person-outline"></ion-icon>Profile
          </a>
      </li>

      <li>
        <a href="jobposting.php">
          <ion-icon name="bag-outline"></ion-icon>Job Postings
      </a>
  </li>

  <li>
    <a href="newjob.php">
      <ion-icon name="add-outline"></ion-icon>Add New Jobs
  </a>
</li>

<li>
    <a href="applist.php">
      <ion-icon name="document-text-outline"></ion-icon>Applications
  </a>
</li>

<li>
    <a href="chate.html">
      <ion-icon name="chatbubbles-outline"></ion-icon> Messages
  </a>
</li>


<li>
    <a href="logout.php">
       <ion-icon name="log-out-outline"></ion-icon>Log out
   </a>
</li>
</ul>



</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Applications</h2>
    <div class="list-view">
        <!-- Application Item -->
        <?php
        if ($stmt->rowCount() > 0) {
            $applications = $stmt->fetchAll();

            foreach ($applications as $application) {
                ?>
                <div class="list-item" data-toggle="modal" data-target="#myModal" onclick="updateModalContent('<?php echo htmlspecialchars(json_encode($application)); ?>')">
                    <img src="system_picture/hot meal bah.jpg" alt="Applicant Photo" class="item-image">
                    <div class="item-content">
                        <div class="item-main-content">Applicant Name: <?php echo htmlspecialchars($application['student']); ?></div>
                        <div class="item-sub-content">
                            <input type="hidden" id="application-id" value="<?php echo ($application['applyid']); ?>">
                            <div class="sub-item">Employer: <?php echo ($application['employer']); ?></div>
                            <div class="sub-item">Apply ID: <?php echo ($application['applyid']); ?></div>
                            <div class="sub-item">Job_Title: <?php echo ($application['jobtitle']); ?></div>
                            <div class="sub-item">Status: <?php echo ($application['status']); ?></div>
                        </div>
                    </div>
                </div>

                <?php
            }

        } 
        else {
            echo "<h1>You Haven't get any application.</h1>";
        }
        ?>

        <!-- Additional Application Items can go here -->

    </div>



    <!--the popup-->

    <!-- Modal Structure -->
    <form action="applist.php" method="POST">
        <input type="hidden" name="application_id" value="<?php echo ($application['applyid']); ?>"> 
        <input type="hidden" name="status" value="Accepted">
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Application Status</h4>
                    </div>
                    <div class="modal-body">
                        <div class="profile-image">
                            <img src="system_picture/hot meal bah.jpg" class="img-responsive img-thumbnail profile-image" alt="Profile Picture" style="max-width: 150px; max-height: 150px; display: block; border: 1px solid #ddd; padding: 5px; border-radius: 5px;" margin: auto;>
                        </div>
                        <table class="table table-striped">
                            <tr><td><strong>Apply Status</strong></td><td id="apply-status"></td></tr>
                            <tr><td><strong>Student Name</strong></td><td id="student-name"></td></tr>
                            <tr><td><strong>Student Email</strong></td><td id="student-email"></td></tr>
                            <tr><td><strong>Matric</strong></td><td id="matric"></td></tr>
                            <tr><td><strong>Faculty</strong></td><td id="faculty"></td></tr>
                            <tr><td><strong>Age</strong></td><td id="age"></td></tr>
                            <tr><td><strong>Employer Name</strong></td><td id="employer-name"></td></tr>
                            <tr><td><strong>Employer Email</strong></td><td id="employer-email"></td></tr>
                            <tr><td><strong>Job Title</strong></td><td id="job-title"></td></tr>
                            <tr><td><strong>Shop Name</strong></td><td id="shop-name"></td></tr>
                            <tr><td><strong>Position</strong></td><td id="position"></td></tr>
                        </table>
                        <div class="buttons">
                            <button class="btn btn-primary update-btn" type="submit" name="accept">Accept</button>
                            <button class="btn btn-primary update-btn" type="submit" name="deny">Deny</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- JavaScript -->
    <script>
        function updateModalContent(applicationData) {
    // Parse application data (stringified in PHP)
            const data = JSON.parse(applicationData);

    // Update modal fields
            document.getElementById('apply-status').innerText = data.status || 'N/A';
            document.getElementById('student-name').innerText = data.student || 'N/A';
            document.getElementById('student-email').innerText = data.student_email || 'N/A';
            document.getElementById('matric').innerText = data.student_matric || 'N/A';
            document.getElementById('faculty').innerText = data.student_faculty || 'N/A';
            document.getElementById('age').innerText = data.student_age || 'N/A';


            document.getElementById('employer-name').innerText = data.employer || 'N/A';
            document.getElementById('employer-email').innerText = data.employer_email || 'N/A';
            document.getElementById('job-title').innerText = data.jobtitle || 'N/A';
            document.getElementById('shop-name').innerText = data.shop || 'N/A';
            document.getElementById('position').innerText = data.position || 'N/A';
        }

        
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</body>
</html>