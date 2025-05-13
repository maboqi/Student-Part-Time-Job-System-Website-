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

// 检查是否提交了表单
if (isset($_GET['jobid'])) {

    $jobid = $_GET['jobid'];
    
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


}

if (isset($_POST['update'])){

    $stmt = $conn->prepare("UPDATE job SET shop = :shop, jobtitle = :jobtitle, salary = :salary, working_days = :working_days, starttime = :starttime, endtime = :endtime, employer = :employer, jobtype = :jobtype, location = :location, des = :des, lv = :lv WHERE jobid = :jobid");

    $jobid = $_POST['jobid'];
    $shop = $_POST['shop'];
    $jobtitle = $_POST['jobtitle'];
    $salary = $_POST['salary'];
    $workingDays = $_POST['working-days'];
    $starttime = $_POST['start-time'];
    $endtime = $_POST['end-time'];
    $employer = $_POST['employer'];
    $jobtype = $_POST['jobtype'];
    $location = $_POST['location'];
    $des = $_POST['des'];
    $lv = $_POST['lv'];
    

    $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
    $stmt->bindParam(':shop', $shop, PDO::PARAM_STR);
    $stmt->bindParam(':jobtitle', $jobtitle, PDO::PARAM_STR);
    $stmt->bindParam(':salary', $salary, PDO::PARAM_STR);
    $stmt->bindParam(':working_days', $workingDays, PDO::PARAM_STR);
    $stmt->bindParam(':starttime', $starttime, PDO::PARAM_STR);
    $stmt->bindParam(':endtime', $endtime, PDO::PARAM_STR);
    $stmt->bindParam(':employer', $employer, PDO::PARAM_STR);
    $stmt->bindParam(':jobtype', $jobtype, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':des', $des, PDO::PARAM_STR);
    $stmt->bindParam(':lv', $lv, PDO::PARAM_STR);

    $stmt->execute();


    $file = $_FILES['image'];
    $maxFileSize = 1 * 1024 * 1024;
    $allowedMimeTypes = ['image/jpeg','image/jpg', 'image/png', 'image/gif'];

    if ($file['error'] === UPLOAD_ERR_OK) {

        if ($file['size'] > $maxFileSize) {
            echo "The file is too large. Maximum allowed size is 1MB.";
            exit;
        }


        $imageData = file_get_contents($file['tmp_name']); // 获取文件的二进制数据
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit;
        }

        // 插入数据到数据库
        $stmt = $conn->prepare("UPDATE job SET pic_data = :pic_data, mime_type = :mime_type WHERE jobid = :jobid;");
        $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
        //$stmt->bindParam(':shop', $shop, PDO::PARAM_STR);
        $stmt->bindParam(':pic_data', $imageData, PDO::PARAM_LOB);
        $stmt->bindParam(':mime_type', $mimeType, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "The image has been successfully uploaded and stored in the database!";
        } else {
            echo "Image upload failed.";
        }
    } 


    $stmt = $conn->prepare("SELECT * FROM job WHERE jobid = :jobid");
    $jobid = $_POST['jobid'];
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
    

}

if (isset($_POST['delete'])){
    $stmt = $conn->prepare("DELETE FROM job WHERE jobid = :jobid");
    $jobid = $_POST['jobid'];
    $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: jobposting.php");
    exit;

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

    <!--myra's new link-->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Frank+Ruhl+Libre&display=swap" rel="stylesheet">

    <title>CampusHired</title>
    <link rel="stylesheet" href="newjob_style.css">

    <style>
        .btn-delete:hover {
            color: #ff1a1a; /* Darker red when hovered */
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
    <!--put your content here-->
    <h2 class="title">Job ID: <?php echo $result["jobid"]; ?></h2>
    

    <div class="purple-background">
        <div class="form-container">

            <div class="tabs">
                <a class="tab active" onclick = "switchTab(event, 'tab_general')">General</a>
                <a class="tab" onclick = "switchTab(event, 'tab_about')">About the job</a>
                <a class="tab" onclick = "switchTab(event, 'tab_des')">Description</a>
                
            </div>

            

            <form action="editjob.php" method="POST" class="form" enctype="multipart/form-data">


                <div id="tab_general" class="tab-content active">
                    <input type="hidden" name="jobid" value="<?php echo ($result['jobid']); ?>">

                    <div class="form-group">
                        <label for="shop-name">Shop Name</label>
                        <input type="text" id="shop-name" name="shop" value="<?php echo $result["shop"]; ?>">
                    </div>
                    <div class="form-group">
                        <label for="employer-name">Employer Name</label>
                        <input type="text" id="employer-name" name="employer" value="<?php echo $result["employer"]; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo $employer["email"]; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $employer["phone"]; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="address-line">Address</label>
                        <input type="text" id="address-line1" name="location" value="<?php echo $result["location"]; ?>"> 
                    </div>
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="text" id="salary" name="salary" value="<?php echo $result["salary"]; ?>" readonly>
                    </div>


                    <div class="form-group-time">
                        <label for="working-time" style = "margin-top: 15px; font-weight: bold;">Working Time</label>
                        <div id="working-time-selector">
                            <label for="start-time">Start Time:</label>
                            <input type="time" id="start-time" name="start-time" required value="<?php echo isset($result['starttime']) ? htmlspecialchars(date('H:i', strtotime($result['starttime']))) : ''; ?>">
                            <label for="end-time">End Time:</label>
                            <input type="time" id="end-time" name="end-time" required value="<?php echo isset($result['endtime']) ? htmlspecialchars(date('H:i', strtotime($result['endtime']))) : ''; ?>">
                        </div>
                    </div>
                </div>


                <div id="tab_about" class="tab-content">


                    <div class="form-group">
                        <label for="shop-name">Job Title</label>
                        <input type="text" id="jobtitle" name="jobtitle" value="<?php echo $result["jobtitle"]; ?>">
                    </div>

                    

                    <div class="form-group">
                        <label for="jobtype">Job Type</label>
                        <div id="jobtype-selector">
                         <label>
                            <input type="radio" name="jobtype" value="Part-Time" required <?php echo $result['jobtype'] === 'Part-Time' ? 'checked' : ''; ?>> Part-Time
                        </label>
                        <label>
                            <input type="radio" name="jobtype" value="Full-Time" required <?php echo $result['jobtype'] === 'Full-Time' ? 'checked' : ''; ?>> Full-Time
                        </label>
                    </div>
                </div>


                <div class="form-group">
                    <label for="job-level">Required Level</label>
                    <input type="text" id="lv" name="lv" value="<?php echo $result["lv"]; ?>">
                </div>

                

                <div class="form-group">
                    <label for="logo-upload">Company Logo</label>
                    <!-- File input for uploading logo -->
                    <input type="file" id="logo-upload" name="image" accept="image/*" onchange="previewLogo(event)">
                    
                </div>

                <script>
                    function previewLogo(event) {
                        const file = event.target.files[0];
                        const preview = document.getElementById("logo-preview");

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                preview.src = e.target.result;
                            preview.style.display = "block"; // Show the preview
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.src = "";
                            preview.style.display = "none"; // Hide the preview if no file is selected
                        }
                    }
                </script>


                <div class="form-group-days">
                    <label for="working-days">Select Working Days</label>
                    <div id="day-selector" class="day-selector">
                        <button type="button" class="day" data-day="Monday">Monday</button>
                        <button type="button" class="day" data-day="Tuesday">Tuesday</button>
                        <button type="button" class="day" data-day="Wednesday">Wednesday</button>
                        <button type="button" class="day" data-day="Thursday">Thursday</button>
                        <button type="button" class="day" data-day="Friday">Friday</button>
                        <button type="button" class="day" data-day="Saturday">Saturday</button>
                        <button type="button" class="day" data-day="Sunday">Sunday</button>
                    </div>
                    <input type="hidden" id="working-days" name="working-days" />
                </div>
                <script>

                    <?php

                    $workingDaysFromDb = isset($result['working_days']) ? $result['working_days'] : ''; 
                    ?>
                    document.addEventListener("DOMContentLoaded", function () {
                        const dayButtons = document.querySelectorAll("#day-selector .day");
                        const workingDaysInput = document.getElementById("working-days");

        // 获取后端传递的工作日数据 (通过 PHP echo 输出)
                        const workingDaysFromDb = "<?php echo htmlspecialchars($workingDaysFromDb); ?>";

        // 将逗号分隔的字符串转为数组
                        const selectedDaysFromDb = workingDaysFromDb.split(", ").map(day => day.trim());

        // 初始化按钮状态
                        dayButtons.forEach((button) => {
                            if (selectedDaysFromDb.includes(button.dataset.day)) {
                button.classList.add("selected"); // 添加选中状态
            }
        });

        // 初始化隐藏输入的值
                        workingDaysInput.value = selectedDaysFromDb.join(", ");

        // 添加点击事件
                        dayButtons.forEach((button) => {
                            button.addEventListener("click", () => {
                // Toggle the "selected" class
                                button.classList.toggle("selected");

                // 更新隐藏输入的值
                                const selectedDays = Array.from(dayButtons)
                                .filter((btn) => btn.classList.contains("selected"))
                                .map((btn) => btn.dataset.day);

                                workingDaysInput.value = selectedDays.join(", ");
                            });
                        });
                    });
                </script>


                <div class="form-group">
                    <!-- Image preview -->
                    <div id="logo-preview-container" style="margin-top: 10px;">
                        <?php 
                        // Convert binary image data to Base64
                        $base64Image = 'data:' . $result['mime_type'] . ';base64,' . base64_encode($result['pic_data']); 
                        ?>
                        <img id="logo-preview" src="<?php echo $base64Image; ?>" alt="Logo Preview" style="max-width: 50px; max-height: 50px; display: block; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                    </div>
                </div>




            </div>

            <div id="tab_des" class="tab-content">
                <div class="form-group">
                    <label for="shop-des">Description</label>
                    <textarea id="description" name="des" class="large-textarea"><?php echo $result["des"]; ?>

                </textarea>
            </div>

        </div>

        <div class="form-actions-container">
            <div class="form-actions">
                <button class = "btn add" type="submit" name = "update">Update</button>
                <button class = "btn cancel" type="reset">Cancel</button>
                <button class = "btn-delete" type="submit" name = "delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
            </div>
        </div>





    </form>

</div>

</div>

</div>

<script>
    function switchTab(event, tabId) {
      // 取消所有选中状态的tab
      document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

      // 激活当前tab和对应的内容
      event.currentTarget.classList.add('active');
      document.getElementById(tabId).classList.add('active');
  }

</script>


</body>


</html>