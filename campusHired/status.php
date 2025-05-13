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


$studentid = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT * FROM application WHERE studentid = :studentid");
$stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if($data){
    $jobid = $data['jobid'];
    $stmt = $conn->prepare("SELECT * FROM job WHERE jobid = :jobid");
    $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    
}






if (isset($_POST['delete'])){
    $stmt = $conn->prepare("DELETE FROM application WHERE applyid = :applyid");
    $applyid = $_POST['del'];
    $stmt->bindParam(':applyid', $applyid, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: status.php");
    exit;

}


?>






<!DOCTYPE html>
<html>
<head>
  
  <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
      <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>CampusHired</title>
  <link rel="stylesheet" href="status_style.css">

</head>
<body>
    <div class="container">
        <!-- 侧边栏 -->
        <div class="sidebar">

          <h1>CampusHired</h1>

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

</div>

<!-- 主要内容区域 -->
<div class="main-content">

    
    <h2>My Application</h2>
    <div class="profile-card">
        <?php
        if (isset($job['pic_data']) && !empty($job['pic_data'])) {
            $base64Image = 'data:' . $job['mime_type'] . ';base64,' . base64_encode($job['pic_data']); 
            ?>
            <img src="<?php echo $base64Image; ?>" alt="Profile Picture">
            <?php
        } else {
            echo '<p style="font-size: 20px; font-weight: bold;">You Haven\'t Apply For Any Job.</p>';
        }
        ?>

        <div class="profile-name">
            <?php echo htmlspecialchars($data['shop'] ?? 'N/A'); ?>
        </div>
        <div class="profile-details">
            <table>
                <tr>
                    <td>Student Name:</td>
                    <td><?php echo htmlspecialchars($data['student'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Apply Status:</td>
                    <td><?php echo htmlspecialchars($data['status'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Student Email:</td>
                    <td><?php echo htmlspecialchars($data['student_email'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Matric:</td>
                    <td><?php echo htmlspecialchars($data['student_matric'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Faculty:</td>
                    <td><?php echo htmlspecialchars($data['student_faculty'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <td>Employer Name:</td>
                    <td><?php echo htmlspecialchars($data['employer'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Employer Email:</td>
                    <td><?php echo htmlspecialchars($data['employer_email'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Job Title:</td>
                    <td><?php echo htmlspecialchars($data['jobtitle'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Shop Name:</td>
                    <td><?php echo htmlspecialchars($data['shop'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Position:</td>
                    <td><?php echo htmlspecialchars($data['position'] ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>

        <form action="status.php" method="POST">
            <input type="hidden" name="del" value="<?php echo $data['applyid']; ?>">
            <button class = "btn-delete" type="submit" name = "delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>

        </form>

        

    </div>
</div>
</div>

</body>
</html>
