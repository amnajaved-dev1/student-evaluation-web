<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';

if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role'])!='admin'){
    header("Location: ../login.php"); exit();
}
?>

<h1>Manage Enrollments</h1>

<table border="1" cellpadding="8">
<tr><th>Student</th><th>Course</th></tr>

<?php
$sql = "
SELECT u.name AS student, c.course_name
FROM enrollments e
JOIN students s ON e.student_id=s.student_id
JOIN users u ON s.user_id=u.user_id
JOIN courses c ON e.course_id=c.course_id
";
$res = mysqli_query($conn, $sql);
while($r = mysqli_fetch_assoc($res)){
    echo "<tr><td>".$r['student']."</td><td>".$r['course_name']."</td></tr>";
}
?>
</table>

<?php include '../includes<footer.php>'; ?>
