<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


?>

<?php
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : null;
?>


<?php
include 'dblocal.php';

$itemsPerPage = 9; // Jobs per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Get filter values from URL parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';
$jobtype = isset($_GET['jobtype']) ? $_GET['jobtype'] : '';

// Database connection and query
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get total count of jobs with filters
    $totalStmt = $conn->prepare("SELECT COUNT(*) AS total FROM job WHERE jobtitle LIKE :search AND (location LIKE :location OR :location = '') AND (lv LIKE :level OR :level = '') AND (jobtype LIKE :jobtype OR :jobtype = '')");
    $totalStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $totalStmt->bindValue(':location', "%$location%", PDO::PARAM_STR);
    $totalStmt->bindValue(':level', "%$level%", PDO::PARAM_STR);
    $totalStmt->bindValue(':jobtype', "%$jobtype%", PDO::PARAM_STR);
    $totalStmt->execute();
    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalJobs = $totalResult['total'];
    $totalPages = ceil($totalJobs / $itemsPerPage);

    // Fetch paginated jobs with filters
    $stmt = $conn->prepare("SELECT * FROM job WHERE jobtitle LIKE :search AND (location LIKE :location OR :location = '') AND (lv LIKE :level OR :level = '') AND (jobtype LIKE :jobtype OR :jobtype = '') LIMIT :offset, :itemsPerPage");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':location', "%$location%", PDO::PARAM_STR);
    $stmt->bindValue(':level', "%$level%", PDO::PARAM_STR);
    $stmt->bindValue(':jobtype', "%$jobtype%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
    <title>CampusHired</title>
    <link rel="stylesheet" href="homee_style.css">

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
            border: 1px solid #ccc;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #555;
            color: #fff;
            border: 1px solid #555;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-bar input,
        .search-bar select {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #45a049;
        }
    </style>

    
</head>
<body>


    <div class = "container">


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




<div class="main-content">

  <?php
  if ($message) {
    echo '<div class="popup" id="popup">';
    echo '<p>' . $message . '</p>';
    echo '<button class="close-btn" onclick="closePopup()">Close</button>';
    echo '</div>';
}
?>

<script>
        // 显示浮窗
    document.addEventListener("DOMContentLoaded", function () {
        const popup = document.getElementById('popup');
        if (popup) {
            popup.style.display = 'block';
        }
    });

        // 关闭浮窗
    function closePopup() {
        const popup = document.getElementById('popup');
        if (popup) {
            popup.style.display = 'none';
        }
    }
</script>


<div class="header">
    <h2>Employer: <?php if(isset($_SESSION["user_id"])) echo $_SESSION["user_id"] ?>, Welcome Back</h2>

    <form class="search-bar" method="GET">
        <input type="text" name="search" placeholder="Search by job title" value="<?= htmlspecialchars($search); ?>">
        <select name="location">
            <option value="">Work Location</option>
            <option value="Dectar" <?= $location == 'Dectar' ? 'selected' : ''; ?>>Dectar</option>
            <option value="KPZ" <?= $location == 'KPZ' ? 'selected' : ''; ?>>KPZ</option>
            <option value="Pusanika" <?= $location == 'Pusanika' ? 'selected' : ''; ?>>Pusanika</option>
            <option value="UKM Bangi" <?= $location == 'UKM Bangi' ? 'selected' : ''; ?>>UKM Bangi</option>
        </select>
        <select name="level">
            <option value="">Student Level</option>
            <option value="Senior Level" <?= $level == 'Senior Level' ? 'selected' : ''; ?>>Senior Level</option>
            <option value="Junior Level" <?= $level == 'Junior Level' ? 'selected' : ''; ?>>Junior Level</option>
        </select>
        <select name="jobtype">
            <option value="">Job Type</option>
            <option value="Part-time" <?= $jobtype == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
            <option value="Full-time" <?= $jobtype == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
        </select>
        <button type="submit">Search/Filter</button>
    </form>

</div>

<h2 style="color: #555; margin-bottom: 20px;">Recommended jobs</h2>


<div class="job-grid">
    <!-- Job Cards (6 additional duplicates) -->

    <?php

    if ($stmt->rowCount() > 0) {
        //$result = $stmt->fetchAll();
        foreach($jobs as $row) {

         echo "<div class='job-card'>";
         echo "<div class='main-content-box'>";

        // biikmarks
         echo "<div class='bookmark-icon'>🔖</div>";

        // job content
         echo "<div class='content-flex'>";
         echo "<div class='content-flex-text'>";
         echo "<p>" . ($row["shop"]) . "</p>";
         echo "<h3>" . ($row["jobtitle"]) . "</h3>";
         echo "</div>";
             // Convert binary image data to Base64
         $base64Image = 'data:' . $row['mime_type'] . ';base64,' . base64_encode($row['pic_data']); 

         echo "<img src='$base64Image'>";
         echo "</div>";

        // label,tag
         echo "<div class='tag-container'>";
         echo "<div class='tag'>" . ($row["jobtype"]) . "</div>";
         echo "<div class='tag'>" . ($row["lv"]) . "</div>";
         echo "</div>";

        echo "</div>"; // 结束 main-content-box

        // 次要内容
        echo "<div class='secondary-content-box'>";
        echo "<div class='salary-location'>";
        echo "<p>RM " . ($row["salary"]) . "/hr</p>";
        echo "<span class='location'>" . ($row["location"]) . "</span>";
        echo "</div>";
        ?>
        <button class='details-button' onclick="window.location.href='formee.php?id=<?php echo $row['jobid']; ?>'">Detail</button>
        <?php
        echo "</div>"; // 结束 secondary-content-box

        echo "</div>"; // 结束 job-card
        
    }

} 

                    //if no jobs found
else {
    echo "<h1>No jobs found</h1>";
}
?>


</div>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1) : ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>&location=<?= $location ?>&level=<?= $level ?>&jobtype=<?= $jobtype ?>">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <a href="?page=<?= $i ?>&search=<?= $search ?>&location=<?= $location ?>&level=<?= $level ?>&jobtype=<?= $jobtype ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages) : ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= $search ?>&location=<?= $location ?>&level=<?= $level ?>&jobtype=<?= $jobtype ?>">Next</a>
    <?php endif; ?>
</div>

</div>

</div>

<hr>


<footer>
    <div class="footerlist">
        <div class="footer1">
            <h3>Copyright © CampusHired Website 2024</h3>
            <p>CampusHired | The universe of UKM Part-time Job</p>
            <hr>

            <br>
            <br>

            <div class="social-icons">
                <a href="https://www.youtube.com" target="_blank">
                  <i class="fab fa-youtube"></i> <!-- YouTube 图标 -->
              </a>
              <a href="https://www.facebook.com/profile.php?id=100094169410267" target="_blank">
                  <i class="fab fa-facebook"></i> <!-- Facebook 图标 -->
              </a>
              <a href="https://twitter.com" target="_blank">
                  <i class="fab fa-twitter"></i> <!-- Twitter 图标 -->
              </a>
          </div>
      </div>


      <div class="footer2">
        <h3>Policy</h3>
        <p>Privacy policy and terms of service.</p>
    </div>
    <div class="footer3">
        <h3>Contact</h3>
        <p>Email: info@yourwebsite.com</p>
        <p>Phone: (123) 456-7890</p>
    </div>
</div>
</footer>

</body>


</html>
