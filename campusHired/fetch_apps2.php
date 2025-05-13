<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


?>

<?php
include "dblocal.php";

// 输出 session 中的 user_id，作为 JSON 的一部分
header('Content-Type: application/json; charset=utf-8'); // 设置响应头

//$job_info = [];
$employer_info = [];

try {
    // 创建 PDO 数据库连接
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM application where studentid = :studentid";
    $stmt = $conn->prepare($sql);
    $studentid = $_SESSION['user_id'];
    $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
    $stmt->execute();
    $app_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $jobid = $app_info['jobid'];
    $employerid = $app_info['employerid'];

    $sql = "SELECT * FROM employer where userid = :employerid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':employerid', $employerid, PDO::PARAM_INT);
    $stmt->execute();
    $employer_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    


} catch (Exception $e) {
    echo json_encode([
        
        //'jobinfo' => []
        'employerinfo' => []

    ]);
    exit;
}

// 输出 JSON 数据
echo json_encode([
    'employerinfo' => $employer_info
]);
?>