<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Get teacher_id
$teacher_res = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id='$user_id'");
$teacher = mysqli_fetch_assoc($teacher_res);
$teacher_id = $teacher['teacher_id'];
?>

<h1>Attendance Report</h1>

<!-- List all courses -->
<h3>Your Courses</h3>
<ul>
    <?php
    $courses = mysqli_query($conn, "SELECT course_id, course_name FROM courses WHERE teacher_id='$teacher_id'");
    while ($c = mysqli_fetch_assoc($courses)) {
        echo "<li><a href='view_attendance.php?course_id=".$c['course_id']."'>".$c['course_name']."</a></li>";
    }
    ?>
</ul>

<?php
// If a specific course is selected, show attendance
if (isset($_GET['course_id'])) {
    $cid = $_GET['course_id'];
    echo "<h2>Attendance for Course: ";
    $cn = mysqli_query($conn, "SELECT course_name FROM courses WHERE course_id='$cid'");
    echo mysqli_fetch_assoc($cn)['course_name'];
    echo "</h2>";

    $report = "
        SELECT s.roll_no, u.name, a.attendance_date, a.status
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        WHERE a.course_id='$cid'
        ORDER BY a.attendance_date DESC
    ";
    $rep_res = mysqli_query($conn, $report);

    if (mysqli_num_rows($rep_res) > 0) {
        echo "<table border='1' cellpadding='8'>
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>";
        while ($r = mysqli_fetch_assoc($rep_res)) {
            echo "<tr>
                    <td>".$r['roll_no']."</td>
                    <td>".$r['name']."</td>
                    <td>".$r['attendance_date']."</td>
                    <td>".$r['status']."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No attendance recorded yet for this course.</p>";
    }
}
?>

<?php
include '../includes/footer.php';
?>
