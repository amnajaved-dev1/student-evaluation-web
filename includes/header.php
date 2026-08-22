<?php
// Start session if not already started
if (!isset($_SESSION)) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user info from session
$name = $_SESSION['name'];
$role = strtolower($_SESSION['role']); // Force role to lowercase for comparison
?>
<!DOCTYPE html>
<html>
<head>
    <title>University Portal</title>

    <!-- MAIN STYLESHEET (LOAD ONCE IN HEAD) -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Logo Section -->
        <div class="sidebar-logo">
            <!-- Change filename or path if your logo file is different -->
            <img src="../assets/images/logo.png" alt="University Logo" class="logo-img">
            <h2>University Portal</h2>
        </div>

        <p>Welcome, <?php echo htmlspecialchars($name); ?></p>

        <ul>
            <?php if($role == 'admin'){ ?>
                <li><a href="../admin/dashboard.php">Dashboard</a></li>
                <li><a href="../admin/admin_analytics.php">Analytics</a></li>
                <li><a href="../admin/manage_users.php">Manage Users</a></li>
                <li><a href="../admin/manage_courses.php">Manage Courses</a></li>
                <li><a href="../admin/manage_enrollments.php">Manage Enrollments</a></li>
                <li><a href="../admin/manage_fees.php">Manage Fees</a></li>
                <li><a href="../admin/manage_attendance.php">Manage Attendance</a></li>

            <?php } elseif($role == 'teacher'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="enter_marks.php">Enter Marks</a></li>
                <li><a href="attendance.php">Mark Attendance</a></li>
                <li><a href="view_attendance.php">View Attendance</a></li>
                <li><a href="analytics.php">Analytics</a></li>

            <?php } elseif($role == 'student'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_marks.php">View Marks</a></li>
                <li><a href="attendance.php">Attendance</a></li>
                <li><a href="evaluate.php">Evaluate Courses</a></li>
            <?php } ?>

            <!-- Logout always at bottom -->
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main content area -->
    <div class="main-content">
