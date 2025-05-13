<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login_admin.php");
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

    $applyid = $_GET['id'];
    
    
    $stmt = $conn->prepare("SELECT * FROM application WHERE applyid = :applyid");
    $stmt->bindParam(':applyid', $applyid, PDO::PARAM_INT);
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


    $studentid = $result['studentid'];
    $stmt = $conn->prepare("SELECT * FROM student WHERE userid = :studentid");
    $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $jobid = $result['jobid'];
    $stmt = $conn->prepare("SELECT * FROM job WHERE jobid = :jobid");
    $stmt->bindParam(':jobid', $jobid, PDO::PARAM_INT);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);


}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx@7.1.0/build/docx.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="validation_style.css">

    <title>Validation Page</title>
    
</head>
<body>
    <div class="container">

        <!-- Sidebar with navigation links -->

        <div class="top-bar">
            <a href="admin.php">
                <ion-icon name="arrow-back-outline"></ion-icon>Back
            </a>
        </div>

        <div class="sidebar">
            <h1>CampusHired</h1>

            <ul>
                <img class="logo" src="system_picture/1.png" alt="UKM Logo" id="picture">




                <li>
                    <a href="logout_admin.php">
                     <ion-icon name="log-out-outline"></ion-icon>Log out
                 </a>
             </li>
         </ul>



     </div>

     <div class="sub-container">
        <div class="left">
            <?php
            $student['pic_data'] = 'data:image/jpeg;base64,' . base64_encode($student['pic_data']);
            ?>
            <img src="<?php echo $student['pic_data']?>" alt="Student Picture">
            <h3>Marcello Muratori</h3>
            <button id="downloadButton"> 
                <ion-icon name="download-outline" style="margin-right: 8px;"></ion-icon>
                Download Application
            </button>
        </div>
        <div class="right">
            <div class="section">
                <h3>Student Basic Information</h3>
                <div class="info">

                    <div><span>Matric Number: </span><?php echo $student['matric']; ?></div>
                    <div><span>Course: </span><?php echo $student['course']; ?></div>
                    <div><span>Faculty: </span><?php echo $student['faculty']; ?></div>
                    <div><span>Level: </span><?php echo $student['studentlv']; ?></div>
                    <div><span>Phone: </span><?php echo $student['phone']; ?></div>
                    <div><span>Email: </span><?php echo $student['email']; ?></div>
                </div>
            </div>
            <div class="section">
                <h3>Job Information</h3>
                <div class="job-info">
                    <?php
                    $job['pic_data'] = 'data:image/jpeg;base64,' . base64_encode($job['pic_data']);
                    ?>

                    <img src="<?php echo $job['pic_data']?>" alt="Shop Logo">
                    <div class="job-info-text">
                        <div><span>Shop:</span><?php echo $job['shop']; ?></div>
                        <div><span>Jobtitle:</span><?php echo $job['jobtitle']; ?></div>
                        <div><span>Jobtype:</span><?php echo $job['jobtype']; ?></div>
                        <div><span>Salary:</span>RM<?php echo $job['salary']; ?>/h</div>
                        <div><span>Position:</span><?php echo $job['location']; ?></div>
                        <div><span>Working-time:</span>
                            <?php
                            echo substr($job['starttime'], 0, 5) . ' - ' . substr($job['endtime'], 0, 5);
                            ?>
                        </div>
                        <div><span>Working-days:</span><?php echo $job['working_days']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    // Collect information
    function collectData() {
        const studentInfo = {
            name: document.querySelector('.left h3').innerText,
            matricNumber: document.querySelector('.info div:nth-child(1)').innerText.split(': ')[1],
            course: document.querySelector('.info div:nth-child(2)').innerText.split(': ')[1],
            faculty: document.querySelector('.info div:nth-child(3)').innerText.split(': ')[1],
            level: document.querySelector('.info div:nth-child(4)').innerText.split(': ')[1],
            phone: document.querySelector('.info div:nth-child(5)').innerText.split(': ')[1],
            email: document.querySelector('.info div:nth-child(6)').innerText.split(': ')[1]
        };

        const jobInfo = {
            shop: document.querySelector('.job-info-text div:nth-child(1)').innerText.split(': ')[1],
            jobTitle: document.querySelector('.job-info-text div:nth-child(2)').innerText.split(': ')[1],
            jobType: document.querySelector('.job-info-text div:nth-child(3)').innerText.split(': ')[1],
            salary: document.querySelector('.job-info-text div:nth-child(4)').innerText.split(': ')[1],
            position: document.querySelector('.job-info-text div:nth-child(5)').innerText.split(': ')[1],
            workingTime: document.querySelector('.job-info-text div:nth-child(6)').innerText.split(': ')[1],
            workingDays: document.querySelector('.job-info-text div:nth-child(7)').innerText.split(': ')[1]
        };

        return { studentInfo, jobInfo };
    }

    // download
    async function downloadApplication() {
    const data = collectData(); // get data

    // create jspdf
    const { jsPDF } = window.jspdf; //get jsPDF 
    const doc = new jsPDF(); // create PDF doc

    const marginLeft = 10;
    let y = 20; // 

    // 添加标题
    doc.setFontSize(20);
    doc.setFont("Helvetica", "bold");
    doc.text('Validation Report', 105, y, { align: 'center' }); // 居中标题
    y += 10;

    // 添加分隔线
doc.setDrawColor(0, 0, 0); // 黑色线
doc.setLineWidth(0.5);
doc.line(marginLeft, y, doc.internal.pageSize.width - marginLeft, y);
y+= 10;


    // 美化并添加 Student Information
doc.setFontSize(14);
doc.text("Student Information", marginLeft, y);
y += 8;

// 添加普通文本 (正常字体)
doc.setFont("Helvetica", "normal"); // 设置字体为正常
doc.setFontSize(12);

Object.entries(data.studentInfo).forEach(([key, value]) => {
        doc.text(`${capitalize(key)}: ${value}`, 10, y); // 添加内容
        y += 8;
    });

    y += 10; // 添加空白间距



    // show data
    //Object.entries(data).forEach(([category, details]) => {
       // doc.text(`[${category}]`, 10, y); // add title
       // y += 10;

       // Object.entries(details).forEach(([key, value]) => {
        //    doc.text(`${key}: ${value}`, 10, y); 
       //     y += 10;
       // });

      //  y += 10; // (" ")
   // });
     // 美化并添加 Job Information
  // 添加间距
    y+= 10;

// Job Information 
doc.setFont("Helvetica", "bold"); // 再次设置字体为加粗
doc.setFontSize(14);
doc.text("Job Information", marginLeft, y);
y += 8;

// 添加普通文本 (正常字体)
doc.setFont("Helvetica", "normal"); // 恢复为正常字体
doc.setFontSize(12);


Object.entries(data.jobInfo).forEach(([key, value]) => {
        doc.text(`${capitalize(key)}: ${value}`, 10, y); // 添加内容
        y += 8;
    });

    // Downdoal pdf
doc.save('Application.pdf'); 
}

function capitalize(word) {
    return word.charAt(0).toUpperCase() + word.slice(1).replace(/_/g, ' ');
}


document.querySelector('#downloadButton').addEventListener('click', downloadApplication);

</script>
</body>
</html>