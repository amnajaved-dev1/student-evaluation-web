<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/header.php';
include '../includes/db.php';

// admin only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'admin') {
    header("Location: ../login.php");
    exit();
}

?>

<h1>Manage Attendance</h1>

<table border="1" cellpadding="8" style="width:100%;">
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Course</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

<?php
$sql = "
SELECT a.attendance_id,
       u.name AS student_name,
       c.course_name,
       a.attendance_date,
       a.status
FROM attendance a
JOIN students s ON a.student_id = s.student_id
JOIN users u ON s.user_id = u.user_id
JOIN courses c ON a.course_id = c.course_id
ORDER BY a.attendance_date DESC
";
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {
    echo "<tr>";
    echo "<td>".$row['attendance_id']."</td>";
    echo "<td>".htmlspecialchars($row['student_name'])."</td>";
    echo "<td>".htmlspecialchars($row['course_name'])."</td>";
    echo "<td>".$row['attendance_date']."</td>";
    echo "<td>".$row['status']."</td>";
    echo "<td>
            <a href='edit_attendance.php?id=".$row['attendance_id']."'>Edit</a> |
            <a href='delete_attendance.php?id=".$row['attendance_id']."' onclick=\"return confirm('Are you sure?')\">Delete</a>
         </td>";
    echo "</tr>";
}
?>

</table>

<?php include '../includes/footer.php'; ?>
