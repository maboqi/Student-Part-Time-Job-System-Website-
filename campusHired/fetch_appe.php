<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


?>


<?php
include "dblocal.php";

//session_start();
//$_SESSION['user_id'] = 1; // 手动设置用户 ID

// 输出 session 中的 user_id，作为 JSON 的一部分
header('Content-Type: application/json; charset=utf-8'); // 设置响应头

$student_info = [];

try {
    // 创建 PDO 数据库连接
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM student";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $student_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($student_info as &$info) {
        if (!empty($info['pic_data'])) {
            
            $info['pic_data'] = 'data:image/jpeg;base64,' . base64_encode($info['pic_data']);
        } else {
            $info['pic_data'] = ''; 
        }
    }


} catch (Exception $e) {
    echo json_encode([
        
        'studentinfo' => []
    ]);
    exit;
}

// 输出 JSON 数据
echo json_encode([
    'studentinfo' => $student_info
]);
?>