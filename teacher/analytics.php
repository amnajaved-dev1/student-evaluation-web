<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

// Get logged in teacher id
$user_id = $_SESSION['user_id'];
$teacher_sql = "SELECT teacher_id FROM teachers WHERE user_id = '$user_id'";
$teacher_result = mysqli_query($conn, $teacher_sql);
$teacher_row = mysqli_fetch_assoc($teacher_result);
$teacher_id = $teacher_row['teacher_id'];

// Query to get marks and attendance
$sql = "
SELECT 
    c.course_id,
    c.course_name,
    AVG(m.quiz) AS avg_quiz,
    AVG(m.assignment) AS avg_assignment,
    AVG(m.midterm) AS avg_midterm,
    AVG(m.final) AS avg_final,
    AVG(m.total) AS avg_total,
    IFNULL(ROUND(SUM(a.status = 'Present') / NULLIF(COUNT(a.status),0) * 100, 2), 0) AS attendance_percentage
FROM courses c
LEFT JOIN marks m ON c.course_id = m.course_id
LEFT JOIN attendance a ON c.course_id = a.course_id
WHERE c.teacher_id = '$teacher_id'
GROUP BY c.course_id
";

$result = mysqli_query($conn, $sql);

// Prepare data for total marks chart
$chartLabels = [];
$chartDataTotal = [];

// We need a separate array to store all rows so we can reuse them later
$allRows = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Store the row for later display
        $allRows[] = $row;

        // Prepare chart data
        $chartLabels[] = $row['course_name'];
        $chartDataTotal[] = round($row['avg_total'], 2);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Analytics</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Teacher Analytics</h1>

<?php if (!empty($chartLabels)) { ?>
    <canvas id="avgChart" width="400" height="200"></canvas>
    <script>
    var ctx = document.getElementById('avgChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Average Total Marks',
                data: <?php echo json_encode($chartDataTotal); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Average Total Marks'
                    }
                }
            }
        }
    });
    </script>
<?php } else { ?>
    <p>No data available to show chart!</p>
<?php } ?>

<table border="1" cellpadding="8" style="margin-top:20px; width:100%;">
    <tr>
        <th>Course</th>
        <th>Avg Quiz</th>
        <th>Avg Assignment</th>
        <th>Avg Midterm</th>
        <th>Avg Final</th>
        <th>Avg Total</th>
        <th>Attendance %</th>
    </tr>

<?php
if (!empty($allRows)) {
    foreach ($allRows as $row) {
        $attPerc = $row['attendance_percentage'];
        // color based on attendance threshold
        $attColor = $attPerc >= 75 ? "green" : "red";

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
        echo "<td>" . ($row['avg_quiz'] !== null ? round($row['avg_quiz'],2) : '-') . "</td>";
        echo "<td>" . ($row['avg_assignment'] !== null ? round($row['avg_assignment'],2) : '-') . "</td>";
        echo "<td>" . ($row['avg_midterm'] !== null ? round($row['avg_midterm'],2) : '-') . "</td>";
        echo "<td>" . ($row['avg_final'] !== null ? round($row['avg_final'],2) : '-') . "</td>";
        echo "<td>" . ($row['avg_total'] !== null ? round($row['avg_total'],2) : '-') . "</td>";
        echo "<td style='color:" . $attColor . "; font-weight:bold;'>"
            . $attPerc . "%</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No analytics data available yet!</td></tr>";
}
?>

</table>

<?php include '../includes/footer.php'; ?>
