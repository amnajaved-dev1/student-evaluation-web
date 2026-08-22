<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Get teacher_id
$res = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id = '$user_id'");
$row = mysqli_fetch_assoc($res);
$teacher_id = $row['teacher_id'];

// Fetch courses for this teacher
$course_sql = "SELECT course_id, course_name FROM courses WHERE teacher_id = '$teacher_id'";
$course_res = mysqli_query($conn, $course_sql);

if(isset($_POST['save_attendance'])) {
    $course_id = $_POST['course_id'];
    $date = $_POST['attendance_date'];

    // Delete existing records for that course and date
    mysqli_query($conn, "DELETE FROM attendance WHERE course_id = '$course_id' AND attendance_date = '$date'");

    // Loop through each student status
    foreach($_POST['status'] as $student_id => $status) {
        $status_val = ($status == 'Present') ? 'Present' : 'Absent';
        mysqli_query($conn, "
            INSERT INTO attendance (student_id, course_id, attendance_date, status)
            VALUES ('$student_id', '$course_id', '$date', '$status_val')
        ");
    }

    $msg = "Attendance saved successfully for course!";
}
?>

<h1>Mark Attendance</h1>

<?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<form method="post">
    <label>Select Course:</label>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php while($c = mysqli_fetch_assoc($course_res)) { ?>
            <option value="<?php echo $c['course_id']; ?>">
                <?php echo $c['course_name']; ?>
            </option>
        <?php } ?>
    </select><br><br>

    <label>Select Date:</label><br>
    <input type="date" name="attendance_date" required><br><br>

    <?php
    // Show students dynamically once a course is selected
    if(isset($_POST['course_id'])) {
        $sel_course = $_POST['course_id'];
        $stu_sql = "
            SELECT s.student_id, u.name, s.roll_no
            FROM students s
            JOIN users u ON s.user_id = u.user_id
            JOIN enrollments e ON s.student_id = e.student_id
            WHERE e.course_id = '$sel_course'
        ";
        $stu_res = mysqli_query($conn, $stu_sql);
    ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Student</th>
                <th>Roll No</th>
                <th>Status</th>
            </tr>
            <?php while($stu = mysqli_fetch_assoc($stu_res)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($stu['name']); ?></td>
                <td><?php echo htmlspecialchars($stu['roll_no']); ?></td>
                <td>
                    <select name="status[<?php echo $stu['student_id']; ?>]">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </td>
            </tr>
            <?php } ?>
        </table><br>

        <button type="submit" name="save_attendance">Save Attendance</button>
    <?php } ?>
</form>

<?php include '../includes/footer.php'; ?>
