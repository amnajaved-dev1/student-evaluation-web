<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';
if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role'])!='admin'){
    header("Location: ../login.php"); exit();
}
?>

<h1>Manage Courses</h1>

<table border="1" cellpadding="8">
    <tr><th>ID</th><th>Course Name</th><th>Teacher</th></tr>

    <?php
    $sql = "
    SELECT c.course_id, c.course_name, u.name AS teacher_name
    FROM courses c
    LEFT JOIN teachers t ON c.teacher_id=t.teacher_id
    LEFT JOIN users u ON t.user_id=u.user_id
    ";
    $res = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($res)){
        echo "<tr>";
        echo "<td>".$row['course_id']."</td>";
        echo "<td>".$row['course_name']."</td>";
        echo "<td>".($row['teacher_name'] ?? 'Unassigned')."</td>";
        echo "</tr>";
    }
    ?>

</table>

<?php include '../includes/footer.php'; ?>
