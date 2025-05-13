<?php
session_start();
include 'dblocal.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['status'] = "Error.";
    $staffid = $_POST['staffid'];
    $pwd = $_POST['password'];
  
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        

        $sql = "SELECT * FROM admin WHERE staffid = :staffid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':staffid', $staffid);
        $stmt->execute();



        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //echo $row['fld_pwd'];
            //echo $row['fld_student_num'];
            //die();
            if ($pwd == $row['pwd']) {

                $_SESSION['user_id'] = $row['staffid'];

                header("Location: admin.php");
                    exit();

            } 
            else {

                $_SESSION['status'] = "Invalid password.";
            }
        }
         else {

            $_SESSION['status'] = "This email is not registerd yet";
        }




    } 
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn = null;

    

    
}
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel='stylesheet' href='login_style.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <section>

        <div class="form-box">
            <div class="form-value">
                <form method="post" action="login_admin.php">

                    <h2>Hello, Welcome to CampusHired</h2>

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
                        <input type="text" name="staffid" placeholder="Staff ID" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <button type="submit" name="login">Sign In</button>
                    <hr>

                </form>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>


