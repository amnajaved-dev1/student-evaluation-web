<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';

// Ensure student role
if(strtolower($_SESSION['role']) != 'student'){
    header("Location: ../login.php");
    exit();
}

$student_user_id = $_SESSION['user_id'];

// fetch student info
$student_info_sql = "
SELECT s.student_id, u.name, u.email 
FROM students s 
JOIN users u ON s.user_id = u.user_id 
WHERE s.user_id='$student_user_id'
";
$info_res = mysqli_query($conn, $student_info_sql);
$student_info = mysqli_fetch_assoc($info_res);
$student_id = $student_info['student_id'];

// GPA / CGPA
$sem_sql = "
SELECT c.semester_id,
       ROUND(AVG(m.grade_point),2) AS sem_gpa
FROM enrollments e
JOIN courses c ON e.course_id = c.course_id
JOIN marks m ON e.student_id = m.student_id AND c.course_id = m.course_id
WHERE e.student_id = '$student_id'
GROUP BY c.semester_id
ORDER BY c.semester_id
";
$sem_res = mysqli_query($conn, $sem_sql);

$total_gp = 0;
$total_sem = 0;
?>

<h1>Student Dashboard</h1>

<h2>Profile</h2>
<ul>
    <li><strong>Name:</strong> <?php echo htmlspecialchars($student_info['name']); ?></li>
    <li><strong>Email:</strong> <?php echo htmlspecialchars($student_info['email']); ?></li>
    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></li>
</ul>

<hr>

<h2>GPA Summary</h2>

<?php if(mysqli_num_rows($sem_res) > 0): ?>
<table border="1" cellpadding="8">
    <tr><th>Semester</th><th>GPA</th></tr>
    <?php
    while($row = mysqli_fetch_assoc($sem_res)){
        echo "<tr>";
        echo "<td>Semester " . htmlspecialchars($row['semester_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sem_gpa']) . "</td>";
        echo "</tr>";

        $total_gp += floatval($row['sem_gpa']);
        $total_sem++;
    }
    $cgpa = ($total_sem > 0) ? round($total_gp / $total_sem, 2) : '-';
    ?>
    <tr>
        <td><strong>CGPA</strong></td>
        <td><strong><?php echo $cgpa; ?></strong></td>
    </tr>
</table>
<?php else: ?>
    <p>No GPA data available yet.</p>
<?php endif; ?>

<hr>

<h2>Enrolled Courses</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Course</th>
        <th>Semester</th>
        <th>Status</th>
    </tr>

<?php
$enr_sql = "
SELECT c.course_id, c.course_name, c.semester_id
FROM enrollments e
JOIN courses c ON e.course_id = c.course_id
WHERE e.student_id = '$student_id'
ORDER BY c.semester_id, c.course_name
";
$enr_res = mysqli_query($conn, $enr_sql);

if(mysqli_num_rows($enr_res) > 0):
    while($course = mysqli_fetch_assoc($enr_res)):
        // check if marks exist (passed / completed)
        $cid = $course['course_id'];
        $mark_check = mysqli_query($conn, "
            SELECT grade FROM marks 
            WHERE student_id = '$student_id' 
              AND course_id = '$cid'
        ");
        $mark_row = mysqli_fetch_assoc($mark_check);

        $status = ($mark_row && !empty($mark_row['grade'])) ? "Completed" : "Enrolled";
?>
        <tr>
            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
            <td><?php echo htmlspecialchars($course['semester_id']); ?></td>
            <td><?php echo $status; ?></td>
        </tr>
<?php
    endwhile;
else:
    echo "<tr><td colspan='3'>No enrolled courses found</td></tr>";
endif;
?>

</table>

<hr>

<h2>Fees</h2>

<?php
$fee_sql = "SELECT * FROM fees WHERE student_id='$student_id' ORDER BY semester_id";
$fee_res = mysqli_query($conn, $fee_sql);

if ($fee_res && mysqli_num_rows($fee_res) > 0):
?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Semester</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Date</th>
        </tr>
        <?php while($f = mysqli_fetch_assoc($fee_res)): ?>
            <tr>
                <td><?php echo htmlspecialchars($f['semester_id']); ?></td>
                <td><?php echo number_format($f['amount'],2); ?></td>
                <td>
                    <?php
                    if($f['paid'] == 1){
                        echo "<span style='color:green;'>Paid</span>";
                    } else {
                        echo "<span style='color:red;'>Unpaid</span>";
                    }
                    ?>
                </td>
                <td><?php echo ($f['payment_date'] ? htmlspecialchars($f['payment_date']) : 'N/A'); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No fee records found.</p>
<?php endif; ?>

<hr>

<p>
    <a href="view_marks.php">View Detailed Marks</a> |
    <a href="attendance.php">View Detailed Attendance</a>
</p>

<?php include '../includes/footer.php'; ?>
