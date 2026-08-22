<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/header.php';
include '../includes/db.php';

// ensure only admin access
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Total counts
$total_students = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
$total_teachers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM teachers"))[0];
$total_courses  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM courses"))[0];
$total_enroll   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM enrollments"))[0];

// Attendance summary: percentage per course
$att_sql = "
SELECT 
    c.course_name,
    IFNULL(ROUND(SUM(a.status='Present') / NULLIF(COUNT(a.status),0)*100,2), 0) AS attendance_pct
FROM courses c
LEFT JOIN attendance a ON c.course_id=a.course_id
GROUP BY c.course_id
";
$att_res = mysqli_query($conn, $att_sql);

// Fees summary
$fee_paid   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM fees WHERE paid=1"))[0];
$fee_unpaid = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM fees WHERE paid=0"))[0];
?>

<h1>Admin Dashboard</h1>

<div style="display:flex; gap:20px; flex-wrap:wrap;">

    <div style="border:1px solid #ccc; padding:15px; width:180px;">
        <h3>Total Students</h3>
        <p><?php echo $total_students; ?></p>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:180px;">
        <h3>Total Teachers</h3>
        <p><?php echo $total_teachers; ?></p>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:180px;">
        <h3>Total Courses</h3>
        <p><?php echo $total_courses; ?></p>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:180px;">
        <h3>Total Enrollments</h3>
        <p><?php echo $total_enroll; ?></p>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:180px;">
        <h3>Fees Paid</h3>
        <p><?php echo $fee_paid; ?></p>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:180px;">
        <h3>Fees Unpaid</h3>
        <p><?php echo $fee_unpaid; ?></p>
    </div>

</div>

<h2>Attendance % by Course</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Course</th>
        <th>Attendance %</th>
    </tr>

    <?php
    if ($att_res && mysqli_num_rows($att_res) > 0) {
        while ($r = mysqli_fetch_assoc($att_res)) {
            $pct = $r['attendance_pct'];
            $color = ($pct >= 75 ? "green" : "red");

            echo "<tr>";
            echo "<td>".htmlspecialchars($r['course_name'])."</td>";
            echo "<td style='color:$color; font-weight:bold;'>".$pct."%</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No attendance data found</td></tr>";
    }
    ?>

</table>

<?php include '../includes/footer.php'; ?>
