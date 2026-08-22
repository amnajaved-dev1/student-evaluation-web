<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';

if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role'])!='admin'){
    header("Location: ../login.php"); exit();
}
?>

<h1>Manage Fees</h1>

<table border="1" cellpadding="8">
<tr><th>Student</th><th>Semester</th><th>Amount</th><th>Paid</th></tr>

<?php
$sql = "
SELECT u.name AS student, f.semester_id, f.amount, f.paid
FROM fees f
JOIN students s ON f.student_id=s.student_id
JOIN users u ON s.user_id=u.user_id
";
$res = mysqli_query($conn, $sql);
while($fee = mysqli_fetch_assoc($res)){
    echo "<tr><td>".$fee['student']."</td><td>".$fee['semester_id']."</td><td>".$fee['amount']."</td><td>".($fee['paid']?'Yes':'No')."</td></tr>";
}
?>

</table>

<?php include '../includes/footer.php'; ?>
