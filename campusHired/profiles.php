
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

  $stmt = $conn->prepare("SELECT * FROM student WHERE userid = :record_id");
  $stmt->bindParam(':record_id', $id, PDO::PARAM_INT);
  $id = $_SESSION['user_id'];

  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$result) {
    echo "No record found such id";
        $result = []; // 如果没有结果，设置为空数组，避免后续访问时报错
      }


    }
    catch(PDOException $e)
    {
      echo "Error: " . $e->getMessage();
    }

    


    if (isset($_POST['update'])) {
      try {
        $stmt = $conn->prepare("UPDATE student SET firstname = :firstname, lastname=:lastname, email = :email, phone = :phone, matric=:matric, faculty=:faculty,course=:course, studentlv=:studentlv WHERE userid = :user_id");

        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':matric', $matric, PDO::PARAM_STR);
        $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':studentlv', $studently, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $matric = $_POST['matric'];
        $faculty = $_POST['faculty'];
        $course = $_POST['course'];
        $studently = $_POST['studently'];
        $user_id = $_SESSION['user_id'];

        $stmt->execute();

        header("Location: profiles.php");
        exit();
      } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
      }
    }

    $conn = null;
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
      <link rel="stylesheet" href="profile_style.css">

    </head>
    <body>


      <div class = "container">
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



   <!--section-->
   <div class="content">
    <h1>Account Settings</h1>

    <div class="profile-card">
      <img src="system_picture/face.jpg" alt="Profile Picture">
      <div class="profile-name"><?php echo $result["firstname"]; ?></div>
      <div class="profile-details">
        <table>
          <tr>
            <td>UserID:</td>
            <td><?php echo $result["userid"]; ?></td>
          </tr>
          <tr>
            <td>Email:</td>
            <td><?php echo $result["email"]; ?></td>
          </tr>
          <tr>
            <td>Phone:</td>
            <td><?php echo $result["phone"]; ?></td>
          </tr>
          <tr>
            <td>Matric Number:</td>
            <td><?php echo $result["matric"]; ?></td>
          </tr>
          <tr>
            <td>Faculty:</td>
            <td><?php echo $result["faculty"]; ?></td>
          </tr>
          <tr>
            <td>Course:</td>
            <td><?php echo $result["course"]; ?></td>
          </tr>
          <tr>
            <td>Level:</td>
            <td><?php echo $result["studentlv"]; ?></td>
          </tr>
        </table>
      </div>
    </div>

    <div class="form-container">

      <!--div class="form-content"-->
      <form method="post" action="profiles.php">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="firstname" value="<?php echo $result["firstname"]; ?>">
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text"  name="lastname" value="<?php echo $result["lastname"]; ?>">
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" value="<?php echo $result["email"]; ?>">
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone"value="<?php echo $result["phone"]; ?>">
        </div>
        <div class="form-group">
          <label>Matric Number</label>
          <input type="text" name="matric" value="<?php echo $result["matric"]; ?>">
        </div>
        <div class="form-group">
          <label>Faculty</label>
          <input type="text"name="faculty" value="<?php echo $result["faculty"]; ?>">
        </div>
        <div class="form-group">
          <label>Course</label>
          <input type="text" name="course" value="<?php echo $result["course"]; ?>">
        </div>
        <div class="form-group">
          <label>Level</label>
          <input type="text" name="studently" value="<?php echo $result["studentlv"]; ?>">
        </div>

        <div class="buttons">
          <button class="update-btn" type = "submit" name = "update">Update</button>
          <button class="cancel-btn" type = "reset">Cancel</button>
        </div>
      </form>
      <!--/div-->

      
    </div>
  </div>
  <!--/section-->

</div>
</body>
</html>
