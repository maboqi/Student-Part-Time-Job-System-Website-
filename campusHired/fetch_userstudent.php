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

$users = [];

try {
    // 创建 PDO 数据库连接
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 准备查询
    $sql = "SELECT employerid, employer FROM application WHERE studentid = :studentid";
    $stmt = $conn->prepare($sql);
    $studentid = $_SESSION['user_id'];
    $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
    $stmt->execute();
    $employers = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $sql = "SELECT * FROM student WHERE userid = :studentid";
    $stmt = $conn->prepare($sql);
    $studentid = $_SESSION['user_id'];
    $stmt->bindParam(':studentid', $studentid, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_id = $user['userid'];
    $user_name = $user['firstname'];


} catch (Exception $e) {
    echo json_encode([
        'error' => "数据库错误: " . $e->getMessage(),
        'user_id' => $_SESSION['user_id'] ?? null,
        'users' => []
    ]);
    exit;
}

// 输出 JSON 数据
echo json_encode([
    'user_id' => $user_id,//employer
    'user_name' => $user_name,
    'users' => $employers //associated students
]);
?>