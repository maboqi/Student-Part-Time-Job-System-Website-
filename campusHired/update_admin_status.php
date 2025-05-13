<?php
include "dblocal.php"; // 数据库连接

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applyid = $_POST['applyid'];
    $status = $_POST['status'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 更新 status 字段
        $stmt = $conn->prepare("UPDATE application SET status = :status WHERE applyid = :applyid");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':applyid', $applyid);
        $stmt->execute();

        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        echo json_encode(['message' => 'Failed to update status: ' . $e->getMessage()]);
    }
}
?>