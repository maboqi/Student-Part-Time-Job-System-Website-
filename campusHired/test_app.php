<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "dblocal.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = $_SESSION['user_id'];

    // Get search and filter inputs
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

    // Pagination variables
    $itemsPerPage = 5; // Maximum number of items per page
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Count total items for pagination
    $countSql = "SELECT COUNT(*) FROM application WHERE employerid = :employerid";
    if (!empty($searchTerm)) {
        $countSql .= " AND jobtitle LIKE :searchTerm";
    }
    if (!empty($statusFilter)) {
        $countSql .= " AND status = :status";
    }
    $countStmt = $conn->prepare($countSql);
    $countStmt->bindParam(':employerid', $user_id, PDO::PARAM_INT);

    if (!empty($searchTerm)) {
        $searchQuery = "%" . $searchTerm . "%";
        $countStmt->bindParam(':searchTerm', $searchQuery, PDO::PARAM_STR);
    }
    if (!empty($statusFilter)) {
        $countStmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Fetch data with limit for pagination
    $sql = "SELECT * FROM application WHERE employerid = :employerid";
    if (!empty($searchTerm)) {
        $sql .= " AND jobtitle LIKE :searchTerm";
    }
    if (!empty($statusFilter)) {
        $sql .= " AND status = :status";
    }
    $sql .= " LIMIT :offset, :itemsPerPage";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':employerid', $user_id, PDO::PARAM_INT);

    if (!empty($searchTerm)) {
        $stmt->bindParam(':searchTerm', $searchQuery, PDO::PARAM_STR);
    }
    if (!empty($statusFilter)) {
        $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>CampusHired - Applications</title>
    <link rel="stylesheet" href="applist_style.css">
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: #555;
            background-color: #f0f0f0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #555;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h1>CampusHired</h1>
            <ul>
                <li><a href="homee.php"><ion-icon name="home-outline"></ion-icon>Dashboard</a></li>
                <li><a href="profilee.php"><ion-icon name="person-outline"></ion-icon>Profile</a></li>
                <li><a href="jobposting.php"><ion-icon name="bag-outline"></ion-icon>Job Postings</a></li>
                <li><a href="newjob.php"><ion-icon name="add-outline"></ion-icon>Add New Jobs</a></li>
                <li><a href="applist.php"><ion-icon name="document-text-outline"></ion-icon>Applications</a></li>
                <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon>Log out</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Job Applications</h2>

            <!-- Search and Filter Section -->
            <form method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search by job title" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select name="status">
                    <option value="">Select Status</option>
                    <option value="Applied" <?php echo ($statusFilter == "Applied") ? "selected" : ""; ?>>Applied</option>
                    <option value="Accepted" <?php echo ($statusFilter == "Accepted") ? "selected" : ""; ?>>Accepted</option>
                    <option value="Validated" <?php echo ($statusFilter == "Validated") ? "selected" : ""; ?>>Validated</option>
                    <option value="Rejected" <?php echo ($statusFilter == "Rejected") ? "selected" : ""; ?>>Rejected</option>
                </select>
                <button type="submit">Search/Filter</button>
            </form>

            <!-- Application List -->
            <div class="list-view">
                <?php
                if ($stmt->rowCount() > 0) {
                    foreach ($applications as $application) {
                        ?>
                        <div class="list-item">
                            <img src="system_picture/hot meal bah.jpg" alt="Applicant Photo" class="item-image">
                            <div class="item-content">
                                <div class="item-main-content">Applicant Name: <?php echo htmlspecialchars($application['student']); ?></div>
                                <div class="item-sub-content">
                                    <div class="sub-item">Employer: <?php echo htmlspecialchars($application['employer']); ?></div>
                                    <div class="sub-item">Apply ID: <?php echo htmlspecialchars($application['applyid']); ?></div>
                                    <div class="sub-item">Job Title: <?php echo htmlspecialchars($application['jobtitle']); ?></div>
                                    <div class="sub-item">Status: <?php echo htmlspecialchars($application['status']); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<h1>No applications found.</h1>";
                }
                ?>
            </div>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($statusFilter); ?>">Previous</a>
                <?php endif; ?>

                <?php
                // Display page numbers
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<a href='?page=$i&search=" . urlencode($searchTerm) . "&status=" . urlencode($statusFilter) . "'";
                    if ($i == $currentPage) {
                        echo " class='active'";
                    }
                    echo ">$i</a>";
                }
                ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($statusFilter); ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
