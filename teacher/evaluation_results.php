<?php
if (session_status()==PHP_SESSION_NONE) session_start();
include '../includes/header.php';
include '../includes/db.php';

if(strtolower($_SESSION['role']) != 'teacher'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$teacher_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id='$user_id'"));
$teacher_id = $teacher_row['teacher_id'];

$sql = "
SELECT 
    c.course_name,
    ROUND(AVG(e.rating),2) AS avg_rating,
    COUNT(e.evaluation_id) AS reviews
FROM evaluations e
JOIN courses c ON e.course_id=c.course_id
WHERE e.teacher_id='$teacher_id'
GROUP BY e.course_id
";

$result = mysqli_query($conn, $sql);
?>

<h1>My Evaluation Results</h1>

<table border="1" cellpadding="8" style="width:100%;">
<tr><th>Course</th><th>Average Rating</th><th>Total Reviews</th></tr>

<?php
while($row=mysqli_fetch_assoc($result)){
    echo "<tr>";
    echo "<td>".htmlspecialchars($row['course_name'])."</td>";
    echo "<td>".($row['avg_rating'] ?? '-')."</td>";
    echo "<td>".$row['reviews']."</td>";
    echo "</tr>";
}
?>
</table>

<?php include '../includes/footer.php'; ?>
