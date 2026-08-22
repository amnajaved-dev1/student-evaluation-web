<?php
if(session_status()==PHP_SESSION_NONE) session_start();
include '../includes/header.php';
include '../includes/db.php';

if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role'])!='admin'){
    header("Location: ../login.php"); exit();
}

$id = intval($_GET['id']);

// fetch record
$record = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM attendance WHERE attendance_id='$id'"));

if(isset($_POST['save'])){
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE attendance_id='$id'");
    header("Location: manage_attendance.php");
    exit();
}
?>

<h1>Edit Attendance</h1>

<form method="post">
    <label>Status</label>
    <select name="status">
        <option value="Present" <?php if($record['status']=='Present') echo "selected"; ?>>Present</option>
        <option value="Absent"  <?php if($record['status']=='Absent') echo "selected"; ?>>Absent</option>
    </select><br><br>
    <button type="submit" name="save">Save</button>
</form>

<?php include '../includes/footer.php'; ?>
