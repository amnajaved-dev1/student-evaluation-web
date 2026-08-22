<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';

if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role'])!='admin'){
    header("Location: ../login.php"); exit();
}
?>

<h1>Manage Users</h1>

<h2>Teachers</h2>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>Name</th><th>Email</th></tr>

<?php
$teacher_sql = "SELECT t.teacher_id, u.name, u.email FROM teachers t JOIN users u ON t.user_id=u.user_id";
$teacher_res = mysqli_query($conn, $teacher_sql);
while($t = mysqli_fetch_assoc($teacher_res)){
    echo "<tr><td>".$t['teacher_id']."</td><td>".$t['name']."</td><td>".$t['email']."</td></tr>";
}
?>
</table>

<h2>Students</h2>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>Name</th><th>Email</th></tr>

<?php
$student_sql = "SELECT s.student_id, u.name, u.email FROM students s JOIN users u ON s.user_id=u.user_id";
$student_res = mysqli_query($conn, $student_sql);
while($s = mysqli_fetch_assoc($student_res)){
    echo "<tr><td>".$s['student_id']."</td><td>".$s['name']."</td><td>".$s['email']."</td></tr>";
}
?>
</table>

<?php include '../includes/footer.php'; ?>
