

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  session_start();

// Paste here
  include "dblocal.php";

  $_SESSION['status'] = "Error: Email is already registered";

  //filter make the output in a certain format even input in another format like <h1>hello</h1>


  $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
  //$name = $_POST['name'];
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);//Clean up email addresses and remove illegal characters
  
  $pwd = filter_var($_POST['password'], FILTER_SANITIZE_STRING);//format not change
  $cpwd = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);
  $role = $_POST['role'];

  $_SESSION["name"] = $name;
  $_SESSION["email"] = $email;
  $_SESSION["pwd"] = $pwd;
  $_SESSION["cpwd"] = $cpwd;
  





  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {// valudate email
    $_SESSION['status'] = "$email is an invalid email address";
    //die();
}

else if($pwd != $cpwd){
        $_SESSION['status'] = "confirm password different, please confirm your password again";
        //die();
    }


else{

    

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      if($role == "student"){
       $stmt = $conn->prepare("INSERT INTO student(userid, firstname, usertype, email, pwd) VALUES (:userid, :user, :usertype, :email, :pwd)");
   }

   if($role == "employer"){
       $stmt = $conn->prepare("INSERT INTO employer(userid, firstname, usertype, email, pwd) VALUES (:userid, :user, :usertype, :email, :pwd)");
   }

      // Prepare the SQL statement

   $_SESSION['new_id'] = $conn->lastInsertId();
   $uid = $_SESSION['new_id'];

      // Bind the parameters
   $stmt->bindParam(':userid', $uid, PDO::PARAM_STR);
   $stmt->bindParam(':user', $name, PDO::PARAM_STR);
   $stmt->bindParam(':usertype', $role, PDO::PARAM_STR);
   $stmt->bindParam(':email', $email, PDO::PARAM_STR);
   $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);

      // Give value to the variables


   $stmt->execute();
      //$_SESSION['new_id'] = $conn->lastInsertId();
   session_unset();


   header("Location:login.php");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Campushired Registration</title>
    <link rel="stylesheet" href="registerform_style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <section>
        <div class="form-box">

            <div class="form_value">

                <form action="registerform.php" method="POST" class="form">
                    <h2>Create New Account</h2>

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
                        <input type="text" name="name" placeholder="Full Name" value="<?php if(isset($_SESSION["name"])) echo $_SESSION["name"] ?>" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" name="email" placeholder="Email Address" value="<?php if(isset($_SESSION["email"])) echo $_SESSION["email"] ?>" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" placeholder="Password" value="<?php if(isset($_SESSION["pwd"])) echo $_SESSION["pwd"] ?>" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="confirm_password" placeholder="Password" value="<?php if(isset($_SESSION["cpwd"])) echo $_SESSION["cpwd"] ?>" required>
                    </div>


                    <div class="radio-group">
                        <input type="radio" id="hiring" name="role" value="employer" required>
                        <label for="hiring">I’m hiring</label>

                        <input type="radio" id="job-seeker" name="role" value="student">
                        <label for="job-seeker">I’m looking for a job</label>
                    </div>
                    <div class="terms">
                        <input type="checkbox" required>
                        <label>I agree to the <a href="#">terms of service</a> and <a href="#">privacy policy</a></label>
                    </div>
                    <button type="submit" class="sign-up" name="create">Sign Up</button>

                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>