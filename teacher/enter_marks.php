<?php
// Don’t call session_start() here — header.php already starts session
include '../includes/header.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Get teacher_id
$teacher_res = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id='$user_id'");
$teacher = mysqli_fetch_assoc($teacher_res);
$teacher_id = $teacher['teacher_id'];

$msg = "";

// Handle form submission
if (isset($_POST['save_marks'])) {

    $course_id = $_POST['course_id'];

    foreach ($_POST['marks'] as $student_id => $fields) {
        $quiz       = intval($fields['quiz']);
        $assignment = intval($fields['assignment']);
        $midterm    = intval($fields['midterm']);
        $final      = intval($fields['final']);
        $total      = $quiz + $assignment + $midterm + $final;

        // Grade logic
        if ($total >= 90)      { $grade = 'A';  $gp = 4.0; }
        elseif ($total >= 80)  { $grade = 'B+'; $gp = 3.3; }
        elseif ($total >= 70)  { $grade = 'B';  $gp = 3.0; }
        elseif ($total >= 60)  { $grade = 'C+'; $gp = 2.3; }
        elseif ($total >= 50)  { $grade = 'C';  $gp = 2.0; }
        else                   { $grade = 'F';  $gp = 0.0; }

        // Insert or update
        $query = "
            INSERT INTO marks (student_id, course_id, quiz, assignment, midterm, final, total, grade, grade_point)
            VALUES ('$student_id','$course_id','$quiz','$assignment','$midterm','$final','$total','$grade','$gp')
            ON DUPLICATE KEY UPDATE
                quiz='$quiz', assignment='$assignment', midterm='$midterm',
                final='$final', total='$total', grade='$grade', grade_point='$gp'
        ";
        mysqli_query($conn, $query);
    }

    $msg = "Marks saved successfully!";
}
?>

<h1>Enter Marks</h1>

<?php if(!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<form method="post" action="enter_marks.php">
    <label>Select Course:</label>
    <select name="course_id" required onchange="this.form.submit()">
        <option value="">-- Select Course --</option>

        <?php
        // Fetch courses for this teacher
        $course_sql = "SELECT course_id, course_name FROM courses WHERE teacher_id='$teacher_id'";
        $course_res = mysqli_query($conn, $course_sql);

        $selectedCourse = isset($_POST['course_id']) ? $_POST['course_id'] : '';

        while ($c = mysqli_fetch_assoc($course_res)) {
            $sel = ($c['course_id'] == $selectedCourse) ? "selected" : "";
            echo "<option value='".$c['course_id']."' $sel>".$c['course_name']."</option>";
        }
        ?>
    </select>
</form>

<?php
// Show student form only if a course is selected
if (!empty($selectedCourse)):
    $stu_sql = "
        SELECT s.student_id, u.name, s.roll_no
        FROM students s
        JOIN users u ON s.user_id = u.user_id
        JOIN enrollments e ON s.student_id = e.student_id
        WHERE e.course_id='$selectedCourse'
    ";
    $stu_res = mysqli_query($conn, $stu_sql);

    if (mysqli_num_rows($stu_res) > 0):
?>

<form method="post" action="enter_marks.php">
    <input type="hidden" name="course_id" value="<?php echo $selectedCourse; ?>">

    <table border="1" cellpadding="8">
        <tr>
            <th>Student</th>
            <th>Roll No</th>
            <th>Quiz</th>
            <th>Assignment</th>
            <th>Midterm</th>
            <th>Final</th>
        </tr>

        <?php while($stu = mysqli_fetch_assoc($stu_res)): ?>

        <?php
        // Pre‑load existing marks (if any)
        $mark_q = mysqli_query($conn, "
            SELECT quiz, assignment, midterm, final
            FROM marks
            WHERE student_id='".$stu['student_id']."' 
              AND course_id='$selectedCourse'
        ");
        $existing = mysqli_fetch_assoc($mark_q);
        ?>

        <tr>
            <td><?php echo htmlspecialchars($stu['name']); ?></td>
            <td><?php echo htmlspecialchars($stu['roll_no']); ?></td>

            <td><input type="number" name="marks[<?php echo $stu['student_id']; ?>][quiz]" min="0" max="10"
                value="<?php echo isset($existing['quiz']) ? $existing['quiz'] : ''; ?>" required></td>

            <td><input type="number" name="marks[<?php echo $stu['student_id']; ?>][assignment]" min="0" max="10"
                value="<?php echo isset($existing['assignment']) ? $existing['assignment'] : ''; ?>" required></td>

            <td><input type="number" name="marks[<?php echo $stu['student_id']; ?>][midterm]" min="0" max="30"
                value="<?php echo isset($existing['midterm']) ? $existing['midterm'] : ''; ?>" required></td>

            <td><input type="number" name="marks[<?php echo $stu['student_id']; ?>][final]" min="0" max="50"
                value="<?php echo isset($existing['final']) ? $existing['final'] : ''; ?>" required></td>
        </tr>

        <?php endwhile; ?>
    </table>

    <br>
    <button type="submit" name="save_marks">Save Marks</button>
</form>

<?php
    else:
        echo "<p>No students enrolled in this course yet!</p>";
    endif;
endif;
?>

<?php include '../includes/footer.php'; ?>
