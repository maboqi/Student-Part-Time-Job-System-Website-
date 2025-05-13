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

    // Get search and filter inputs
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

    // Pagination variables
    $itemsPerPage = 5; // Maximum number of items per page
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Count total items for pagination
    $countSql = "SELECT COUNT(*) FROM application WHERE employerid = :employerid";
    if (!empty($searchTerm)) {
        $countSql .= " AND jobtitle LIKE :searchTerm";
    }
    if (!empty($statusFilter)) {
        $countSql .= " AND status = :status";
    }
    $countStmt = $conn->prepare($countSql);
    $countStmt->bindParam(':employerid', $user_id, PDO::PARAM_INT);

    if (!empty($searchTerm)) {
        $searchQuery = "%" . $searchTerm . "%";
        $countStmt->bindParam(':searchTerm', $searchQuery, PDO::PARAM_STR);
    }
    if (!empty($statusFilter)) {
        $countStmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Fetch data with limit for pagination
    $sql = "SELECT * FROM application WHERE employerid = :employerid";
    if (!empty($searchTerm)) {
        $sql .= " AND jobtitle LIKE :searchTerm";
    }
    if (!empty($statusFilter)) {
        $sql .= " AND status = :status";
    }
    $sql .= " LIMIT :offset, :itemsPerPage";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':employerid', $user_id, PDO::PARAM_INT);

    if (!empty($searchTerm)) {
        $stmt->bindParam(':searchTerm', $searchQuery, PDO::PARAM_STR);
    }
    if (!empty($statusFilter)) {
        $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

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
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: #555;
            background-color: #f0f0f0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #555;
            color: #fff;
        }
    </style>
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

    <div class="header">

        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by job title" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <select name="status">
                <option value="">Select Status</option>
                <option value="Applied" <?php echo ($statusFilter == "Applied") ? "selected" : ""; ?>>Applied</option>
                <option value="Accepted" <?php echo ($statusFilter == "Accepted") ? "selected" : ""; ?>>Accepted</option>
                <option value="Validated" <?php echo ($statusFilter == "Validated") ? "selected" : ""; ?>>Validated</option>
                <option value="Denied" <?php echo ($statusFilter == "Denied") ? "selected" : ""; ?>>Denied</option>
            </select>
            <button type="submit">Search/Filter</button>
        </form>
    </div>
    <h2>Applications</h2>


    <div class="list-view">
        <!-- Application Item -->
        <?php
        if ($stmt->rowCount() > 0) {
            //$applications = $stmt->fetchAll();

            foreach ($applications as $application) {
                ?>
                <div class="list-item" data-toggle="modal" data-target="#myModal" onclick="updateModalContent('<?php echo htmlspecialchars(json_encode($application)); ?>')">
                    <?php 
                    $jobid = $application['jobid'];
                    $stmt = $conn->prepare("SELECT * FROM job WHERE jobid = :jobid");
                    $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
                    $stmt->execute();
                    $job = $stmt->fetch(PDO::FETCH_ASSOC);

                    $base64Image = 'data:' . $job['mime_type'] . ';base64,' . base64_encode($job['pic_data']); 
                    ?>
                    <img src="<?php echo $base64Image; ?>" alt="Applicant Photo" class="item-image">

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

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($statusFilter); ?>">Previous</a>
        <?php endif; ?>

        <?php
                // Display page numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?page=$i&search=" . urlencode($searchTerm) . "&status=" . urlencode($statusFilter) . "'";
            if ($i == $currentPage) {
                echo " class='active'";
            }
            echo ">$i</a>";
        }
        ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($statusFilter); ?>">Next</a>
        <?php endif; ?>
    </div>


</div>
</div>



<!--the popup-->

<!-- Modal Structure -->



<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Application Status</h4>
            </div>
            <div class="modal-body">
                <div class="profile-image">
                    <img src="system_picture/work.jpg" class="img-responsive img-thumbnail profile-image" alt="Profile Picture">
                </div>
                <table class="table table-striped">
                    <tr><td><strong>Apply ID</strong></td><td id="apply-id"></td></tr>
                    <tr><td><strong>Apply Status</strong></td><td id="apply-status"></td></tr>
                    <tr><td><strong>Student Name</strong></td><td id="student-name"></td></tr>
                    <tr><td><strong>Student Email</strong></td><td id="student-email"></td></tr>
                    <tr><td><strong>Matric</strong></td><td id="matric"></td></tr>
                    <tr><td><strong>Faculty</strong></td><td id="faculty"></td></tr>

                    <tr><td><strong>Employer Name</strong></td><td id="employer-name"></td></tr>
                    <tr><td><strong>Employer Email</strong></td><td id="employer-email"></td></tr>
                    <tr><td><strong>Job Title</strong></td><td id="job-title"></td></tr>
                    <tr><td><strong>Shop Name</strong></td><td id="shop-name"></td></tr>
                    <tr><td><strong>Position</strong></td><td id="position"></td></tr>
                </table>
                <div class="buttons">
                    <form method="POST" action="update_status.php">
                        <input type="hidden" id="in-applyid" name="applyid">
                        <input type="hidden" id="in-status" name="status">
                        <button class="btn btn-primary update-btn" type="submit" onclick="setStatus('Accepted')">Accept</button>
                        <button class="btn btn-danger update-btn" type="submit" onclick="setStatus('Denied')">Deny</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- JavaScript -->
<script>
    function updateModalContent(applicationData) {
    // Parse application data (stringified in PHP)
        const data = JSON.parse(applicationData);

    // Update modal fields
        document.getElementById('apply-id').innerText = data.applyid || 'N/A';
        document.getElementById('apply-status').innerText = data.status || 'N/A';
        document.getElementById('student-name').innerText = data.student || 'N/A';
        document.getElementById('student-email').innerText = data.student_email || 'N/A';
        document.getElementById('matric').innerText = data.student_matric || 'N/A';
        document.getElementById('faculty').innerText = data.student_faculty || 'N/A';
            //document.getElementById('age').innerText = data.student_age || 'N/A';


        document.getElementById('employer-name').innerText = data.employer || 'N/A';
        document.getElementById('employer-email').innerText = data.employer_email || 'N/A';
        document.getElementById('job-title').innerText = data.jobtitle || 'N/A';
        document.getElementById('shop-name').innerText = data.shop || 'N/A';
        document.getElementById('position').innerText = data.position || 'N/A';
    }

    function setStatus(value) {
        const idElement = document.getElementById('apply-id');
            //const statusElement2 = document.getElementById('apply-status');
        const idValue = idElement.innerText;
        const statusValue = value;

        const idInput = document.getElementById('in-applyid');
        const statusInput = document.getElementById('in-status');

// 设置 value 属性
        idInput.value = idValue;
        statusInput.value = statusValue;

    }




</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>




</body>
</html>