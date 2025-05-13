<?php
include 'dblocal.php';
?>


<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  session_start();

  $_SESSION['status'] = "Error";

// Paste here
  include "dblocal.php";

  //filter make the output in a certain format even input in another format like <h1>hello</h1>


  //$userid = filter_var($_POST['userid'], FILTER_SANITIZE_STRING);
  $userid = $_POST['userid'];
  //$name = $_POST['name'];
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);//Clean up email addresses and remove illegal characters
  
  $pwd = filter_var($_POST['password'], FILTER_SANITIZE_STRING);//format not change
  $cpwd = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);
  $role = $_POST['role'];
  

  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {// valudate email
    //echo "<script>alert('$email is an invalid email address');</script>";
    //die();
    $_SESSION['status'] = "$email is an invalid email address";
    //header('location: pwdupdate.php');
    //die();
}

else if($pwd != $cpwd){
        //echo "confirm password different, please confirm your password again";
    $_SESSION['status'] = "confirm password different, please confirm your password again";
    //exit();
}


else{

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      if($role == "student"){
        $stmt = $conn->prepare("SELECT * FROM student WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $editstudent = $stmt->fetch(PDO::FETCH_ASSOC);
            $getid = $editstudent['userid'];

            if($userid == $getid){
                $stmt = $conn->prepare("UPDATE student SET pwd = :pwd WHERE userid = :getid");

                $stmt->bindParam(':getid', $getid, PDO::PARAM_STR);
                $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);

                $stmt->execute();
            }
            if($userid != $getid){
                //echo 
                $_SESSION['status'] ="userid not match email, try again";
                //die();
            }
        }
        else{
            //echo
            $_SESSION['status'] = "the email is not registered in our system";
            //die();
        }
        

    }

    if($role == "employer"){
        $stmt = $conn->prepare("SELECT * FROM employer WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $editemployer = $stmt->fetch(PDO::FETCH_ASSOC);
            $getid = $editemployer['userid'];

            if($userid == $getid){
                $stmt = $conn->prepare("UPDATE employer SET pwd = :pwd WHERE userid = :getid");

                $stmt->bindParam(':getid', $getid, PDO::PARAM_STR);
                $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);
                
                $stmt->execute();
            }
            
            if($userid != $getid){
                //echo 
                $_SESSION['status'] ="userid not match email, try again";
                //die();
            }
        }
        else{
            $_SESSION['status'] = "the email is not registered in our system";
            //die();
        }
        

    }

    if($_SESSION['status'] == "Error"){
        header("Location:login.php");
    }


    
}

catch(PDOException $e)
{
  echo "Error: " . $e->getMessage();
}

$conn = null;

}


}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel='stylesheet' href='pwd_style.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reset Password</title>
    
</head>
<body>


    <section>


        <div class="form-box">

            <div>



            </div>


            <div class="form-value">
                <form method="post" action="pwdupdate.php">
                    <h2>Password Reset</h2>

                    <div class = "alert">
                        <?php 
                        if (isset($_SESSION['status'])) {
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['status']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php
                            unset($_SESSION['status']);
                        }
                        ?>
                        
                    </div>

                    

                    <div class="inputbox">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" name="userid" placeholder="UserID" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" name="email" placeholder="Email Address" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" placeholder="New Password" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="confirm_password" placeholder="Confir Your Password" required>
                    </div>

                    <div class="radio-group">
                        <input type="radio" id="hiring" name="role" value="employer" required>
                        <label for="hiring">I’m hiring</label>

                        <input type="radio" id="job-seeker" name="role" value="student">
                        <label for="job-seeker">I’m looking for a job</label>
                    </div>

                    <button type="submit" name="pwdupdate">Update Password</button>

                </form>

            </div>
        </div>

    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
