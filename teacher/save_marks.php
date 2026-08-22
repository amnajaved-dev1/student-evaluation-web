<?php
session_start();
include '../includes/db.php';

if(isset($_POST['save_marks'])){
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $quiz = $_POST['quiz'];
    $assignment = $_POST['assignment'];
    $midterm = $_POST['midterm'];
    $final = $_POST['final'];
    $total = $quiz + $assignment + $midterm + $final;

    // Calculate grade (simple example)
    if($total >= 90) { $grade='A'; $gp=4.0; }
    elseif($total >= 80) { $grade='B+'; $gp=3.3; }
    elseif($total >= 70) { $grade='B'; $gp=3.0; }
    elseif($total >= 60) { $grade='C+'; $gp=2.3; }
    elseif($total >= 50) { $grade='C'; $gp=2.0; }
    else { $grade='F'; $gp=0.0; }

    // Insert into marks table
    $query = "INSERT INTO marks (student_id, course_id, quiz, assignment, midterm, final, total, grade, grade_point)
              VALUES ('$student_id','$course_id','$quiz','$assignment','$midterm','$final','$total','$grade','$gp')
              ON DUPLICATE KEY UPDATE quiz='$quiz', assignment='$assignment', midterm='$midterm', final='$final', total='$total', grade='$grade', grade_point='$gp'";

    if(mysqli_query($conn, $query)){
        echo "Marks saved successfully for student ID: $student_id<br>";
    } else {
        echo "Error: ".mysqli_error($conn);
    }
}

echo "<br><a href='enter_marks.php'>Back</a>";
?>
