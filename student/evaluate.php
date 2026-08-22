<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include '../includes/header.php';
include '../includes/db.php';

if(strtolower($_SESSION['role']) != 'student'){
    header("Location: ../login.php");
    exit();
}

$student_user_id = $_SESSION['user_id'];

// get internal student_id
$stu_sql = "SELECT student_id FROM students WHERE user_id='$student_user_id'";
$stu_res = mysqli_query($conn, $stu_sql);
$stu_row = mysqli_fetch_assoc($stu_res);
$student_id = $stu_row['student_id'];

if(isset($_POST['submit_eval'])){
    $parts = explode("|", $_POST['course_teacher']);
    $course_id = intval($parts[0]);
    $teacher_id = intval($parts[1]);
    $semester_id= intval($_POST['semester_id']);
    $rating     = intval($_POST['rating']);
    $comments   = mysqli_real_escape_string($conn, $_POST['comments']);

    $insert_sql = "
    INSERT INTO evaluations (student_id, course_id, teacher_id, semester_id, rating, comments)
    VALUES ('$student_id','$course_id','$teacher_id','$semester_id','$rating','$comments')
    ON DUPLICATE KEY UPDATE
        rating='$rating',
        comments='$comments'
    ";
    mysqli_query($conn, $insert_sql);
    $msg = "Your evaluation has been saved.";
}
?>

<h1>Course Evaluation</h1>

<?php if(isset($msg)) echo "<p class='alert alert-success'>".$msg."</p>"; ?>

<form method="POST">

    <label>Select Semester</label><br>
    <select name="semester_id" required>
        <option value="">-- Select Semester --</option>
        <?php
        $sem_sql = "
        SELECT DISTINCT c.semester_id
        FROM enrollments e
        JOIN courses c ON e.course_id=c.course_id
        WHERE e.student_id='$student_id'
        ORDER BY c.semester_id
        ";
        $sem_res = mysqli_query($conn, $sem_sql);
        while($s = mysqli_fetch_assoc($sem_res)){
            echo "<option value='".$s['semester_id']."'>Semester ".$s['semester_id']."</option>";
        }
        ?>
    </select><br><br>

    <label>Select Course</label><br>
    <select name="course_teacher" required>
        <option value="">-- Select Course --</option>
        <?php
        $course_sql = "
        SELECT c.course_id, c.course_name, t.teacher_id, u.name AS teacher_name, c.semester_id
        FROM enrollments e
        JOIN courses c ON e.course_id=c.course_id
        JOIN teachers t ON c.teacher_id=t.teacher_id
        JOIN users u ON t.user_id=u.user_id
        WHERE e.student_id='$student_id'
        ";
        $course_res = mysqli_query($conn, $course_sql);
        while($c = mysqli_fetch_assoc($course_res)){
            echo '<option value="'.$c['course_id'].'|'.$c['teacher_id'].'">Sem '.$c['semester_id'].' - '.$c['course_name'].' ('.$c['teacher_name'].')</option>';
        }
        ?>
    </select><br><br>

    <label>Rating</label><br>
    <select name="rating" required>
        <option value="">-- Select Rating --</option>
        <option value="5">5 ‐ Excellent</option>
        <option value="4">4 ‐ Very Good</option>
        <option value="3">3 ‐ Good</option>
        <option value="2">2 ‐ Fair</option>
        <option value="1">1 ‐ Poor</option>
    </select><br><br>

    <label>Comments</label><br>
    <textarea name="comments" rows="3"></textarea><br><br>

    <button type="submit" name="submit_eval">Submit Evaluation</button>

</form>

<?php include '../includes/footer.php'; ?>
