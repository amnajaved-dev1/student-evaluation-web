<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';

$student_user_id = $_SESSION['user_id'];

$stu_id_sql = "SELECT student_id FROM students WHERE user_id='$student_user_id'";
$stu_res = mysqli_query($conn, $stu_id_sql);
$stu_row = mysqli_fetch_assoc($stu_res);
$student_id = $stu_row['student_id'];
?>

<h1>My Detailed Marks</h1>

<table border="1" cellpadding="8">
<tr>
    <th>Course</th><th>Quiz</th><th>Assignment</th><th>Midterm</th><th>Final</th>
    <th>Total</th><th>Grade</th><th>Grade Point</th>
</tr>

<?php
$sql = "
SELECT c.course_name, m.quiz, m.assignment, m.midterm, m.final, m.total, m.grade, m.grade_point
FROM enrollments e
JOIN courses c ON e.course_id=c.course_id
LEFT JOIN marks m ON e.student_id=m.student_id AND e.course_id=m.course_id
WHERE e.student_id='$student_id'
ORDER BY c.semester_id, c.course_name
";
$res = mysqli_query($conn, $sql);

if(mysqli_num_rows($res) > 0){
    while($r = mysqli_fetch_assoc($res)){
        echo "<tr>";
        echo "<td>".htmlspecialchars($r['course_name'])."</td>";
        echo "<td>".$r['quiz']."</td>";
        echo "<td>".$r['assignment']."</td>";
        echo "<td>".$r['midterm']."</td>";
        echo "<td>".$r['final']."</td>";
        echo "<td>".$r['total']."</td>";
        echo "<td>".$r['grade']."</td>";
        echo "<td>".$r['grade_point']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No marks found</td></tr>";
}
?>

</table>

<?php include '../includes/footer.php'; ?>
