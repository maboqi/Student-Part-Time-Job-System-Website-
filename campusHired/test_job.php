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

    // Pagination settings
    $limit = 5; // Records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Search and filter
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_status = isset($_GET['status']) ? $_GET['status'] : '';

    // Base query
    $query = "SELECT * FROM application WHERE employerid = :employerid";
    $params = [':employerid' => $user_id];

    // Add search and filter conditions
    if (!empty($search_query)) {
        $query .= " AND jobtitle LIKE :search";
        $params[':search'] = '%' . $search_query . '%';
    }
    if (!empty($filter_status)) {
        $query .= " AND status = :status";
        $params[':status'] = $filter_status;
    }

    // Pagination limit
    $query .= " LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetchAll();

    // Count total records for pagination
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM application WHERE employerid = :employerid");
    $count_stmt->bindParam(':employerid', $user_id, PDO::PARAM_INT);
    $count_stmt->execute();
    $total_apps = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate total pages
    $total_pages = ceil($total_apps / $limit);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="applist_style.css">
    <title>CampusHired - Applications</title>
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

        <div class="main-content">
            <div class="header">
                <form method="GET" action="" class="search-bar">
                    <input type="text" name="search" placeholder="Search by job title" value="<?php echo htmlspecialchars($search_query); ?>">
                    <select name="status">
                        <option value="">Select Status</option>
                        <option value="Applied" <?php if ($filter_status == "Applied") echo "selected"; ?>>Applied</option>
                        <option value="Accepted" <?php if ($filter_status == "Accepted") echo "selected"; ?>>Accepted</option>
                        <option value="Validated" <?php if ($filter_status == "Validated") echo "selected"; ?>>Validated</option>
                        <option value="Rejected" <?php if ($filter_status == "Rejected") echo "selected"; ?>>Rejected</option>
                    </select>
                    <button type="submit">Search/Filter</button>
                </form>
            </div>

            <h2 style="color: #555; margin-bottom: 20px;">My Applications</h2>

            <div class="list-view">
                <?php
                if (count($result) > 0) {
                    foreach ($result as $application) {
                        ?>
                        <div class="list-item">
                            <img src="system_picture/hot meal bah.jpg" alt="Applicant Photo" class="item-image">
                            <div class="item-content">
                                <div class="item-main-content">
                                    Applicant Name: <?php echo htmlspecialchars($application['student']); ?> 
                                    | Job Title: <?php echo htmlspecialchars($application['jobtitle']); ?>
                                </div>
                                <div class="item-sub-content">
                                    <div class="sub-item">Apply ID: <?php echo htmlspecialchars($application['applyid']); ?></div>
                                    <div class="sub-item">Status: <?php echo htmlspecialchars($application['status']); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // Dynamic message for no results
                    if (!empty($search_query) || !empty($filter_status)) {
                        echo "<h1>No applications found matching your criteria.</h1>";
                    } else {
                        echo "<h1>No applications yet!</h1>";
                    }
                }
                ?>
            </div>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
