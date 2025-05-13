<?php

include 'dblocal.php';

//update
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

        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>