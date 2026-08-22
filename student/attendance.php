<?php
if(!isset($_SESSION)) session_start();
include '../includes/header.php';
include '../includes/db.php';

if(strtolower($_SESSION['role']) != 'student'){
    header("Location: ../login.php");
    exit();
}

$student_user_id = $_SESSION['user_id'];

// get student_id
$stu_sql = "SELECT student_id FROM students WHERE user_id = '$student_user_id'";
$stu_res = mysqli_query($conn, $stu_sql);
$stu_row = mysqli_fetch_assoc($stu_res);
$student_id = $stu_row['student_id'];
?>

<h1>My Attendance Summary</h1>

<table border="1" cellpadding="8" style="width:100%;">
<tr>
    <th>Course</th>
    <th>Present</th>
    <th>Absent</th>
    <th>Total</th>
    <th>Attendance %</th>
</tr>

<?php
$sql = "
SELECT 
    c.course_name,
    SUM(a.status = 'Present') AS present_count,
    SUM(a.status = 'Absent') AS absent_count,
    COUNT(a.status) AS total_count,
    IFNULL(
        ROUND(SUM(a.status = 'Present') / NULLIF(COUNT(a.status),0) * 100, 2),
        0
    ) AS attendance_percentage
FROM enrollments e
JOIN courses c ON e.course_id = c.course_id
LEFT JOIN attendance a ON e.student_id = a.student_id AND e.course_id = a.course_id
WHERE e.student_id = '$student_id'
GROUP BY e.course_id
ORDER BY c.semester_id, c.course_name
";

$res = mysqli_query($conn, $sql);

$att_chart_labels = [];
$att_chart_data   = [];

if($res && mysqli_num_rows($res) > 0){
    while($row = mysqli_fetch_assoc($res)){
        // For table
        $pct = $row['attendance_percentage'];
        $color = ($pct >= 75 ? "green" : "red"); // color coding

        echo "<tr>";
        echo "<td>".htmlspecialchars($row['course_name'])."</td>";
        echo "<td>".$row['present_count']."</td>";
        echo "<td>".$row['absent_count']."</td>";
        echo "<td>".$row['total_count']."</td>";
        echo "<td style='color: $color; font-weight:bold;'>".$pct."%</td>";
        echo "</tr>";

        // Prepare for chart
        $att_chart_labels[] = $row['course_name'];
        $att_chart_data[]   = $pct;
    }
} else {
    echo "<tr><td colspan='5'>No attendance records found!</td></tr>";
}
?>

</table>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h2>Attendance Trend Chart</h2>

<canvas id="attendanceTrendChart" width="400" height="200"></canvas>

<script>
var ctx = document.getElementById('attendanceTrendChart').getContext('2d');
var attendanceTrendChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($att_chart_labels); ?>,
        datasets: [{
            label: 'Attendance %',
            data: <?php echo json_encode($att_chart_data); ?>,
            backgroundColor: <?php echo json_encode(array_map(function($val){
                return $val >= 75 ? 'rgba(75, 192, 192, 0.6)' : 'rgba(255, 99, 132, 0.6)';
            }, $att_chart_data)); ?>,
            borderColor: <?php echo json_encode(array_map(function($val){
                return $val >= 75 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)';
            }, $att_chart_data)); ?>,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                title: {
                    display: true,
                    text: 'Attendance Percentage'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + "%";
                    }
                }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
