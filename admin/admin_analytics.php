<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/header.php';
include '../includes/db.php';

// only admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'admin') {
    header("Location: ../login.php");
    exit();
}

// — Attendance Data — //
$att_sql = "
SELECT c.course_name,
       IFNULL(ROUND(SUM(a.status='Present') / NULLIF(COUNT(a.status),0)*100,2), 0) AS attendance_pct
FROM courses c
LEFT JOIN attendance a ON c.course_id=a.course_id
GROUP BY c.course_id
";
$att_res = mysqli_query($conn, $att_sql);

$att_labels = [];
$att_data   = [];

while ($r = mysqli_fetch_assoc($att_res)) {
    $att_labels[] = $r['course_name'];
    $att_data[]   = $r['attendance_pct'];
}

// — GPA by Semester — //
$gpa_sql = "
SELECT c.semester_id,
       ROUND(AVG(m.grade_point),2) AS avg_gpa
FROM marks m
JOIN courses c ON m.course_id=c.course_id
GROUP BY c.semester_id
ORDER BY c.semester_id
";
$gpa_res = mysqli_query($conn, $gpa_sql);

$gpa_labels = [];
$gpa_data   = [];

while ($g = mysqli_fetch_assoc($gpa_res)) {
    $gpa_labels[] = "Sem " . $g['semester_id'];
    $gpa_data[]   = $g['avg_gpa'];
}

// — Fees Summary (bar) — //
$fee_sql = "
SELECT 
    f.semester_id,
    SUM(f.amount) AS total_amount,
    SUM(f.paid) AS paid_count,
    COUNT(*) AS total_count
FROM fees f
GROUP BY f.semester_id
ORDER BY f.semester_id
";
$fee_res = mysqli_query($conn, $fee_sql);

$fee_labels = [];
$fee_paid   = [];
$fee_unpaid = [];

while ($f = mysqli_fetch_assoc($fee_res)) {
    $fee_labels[] = "Sem " . $f['semester_id'];
    $fee_paid[]   = intval($f['paid_count']);
    $fee_unpaid[] = intval($f['total_count'] - $f['paid_count']);
}

// — Evaluation Analytics — //
$eval_sql = "
SELECT c.course_name, ROUND(AVG(e.rating),2) AS avg_rating
FROM evaluations e
JOIN courses c ON e.course_id=c.course_id
GROUP BY e.course_id
";
$eval_res = mysqli_query($conn, $eval_sql);

$eval_labels = [];
$eval_data   = [];

while ($er = mysqli_fetch_assoc($eval_res)) {
    $eval_labels[] = $er['course_name'];
    $eval_data[]   = $er['avg_rating'];
}
?>

<h1>Admin Analytics</h1>

<h2 style="margin-top:30px;">📊 Attendance % by Course</h2>
<div class="chart-trigger">
    <canvas id="attendanceChart" width="700" height="300"></canvas>
</div>

<h2 style="margin-top:30px;">📈 Average GPA by Semester</h2>
<div class="chart-trigger">
    <canvas id="gpaChart" width="700" height="300"></canvas>
</div>

<h2 style="margin-top:30px;">💰 Fees Paid vs Unpaid by Semester</h2>
<div class="chart-trigger">
    <canvas id="feesBarChart" width="700" height="250"></canvas>
</div>

<h2 style="margin-top:30px;">⭐ Evaluation Ratings by Course</h2>
<div class="chart-trigger">
    <canvas id="evalChart" width="700" height="300"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart initialization functions
function buildAttendanceChart() {
    new Chart(document.getElementById('attendanceChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($att_labels); ?>,
            datasets: [{
                label: 'Attendance %',
                data: <?php echo json_encode($att_data); ?>,
                backgroundColor: <?php echo json_encode(array_map(function($val){
                    return $val >= 75 
                        ? 'rgba(75, 192, 192, 0.7)' 
                        : 'rgba(255, 99, 132, 0.7)';
                }, $att_data)); ?>,
                borderColor: <?php echo json_encode(array_map(function($val){
                    return $val >= 75 
                        ? 'rgba(75, 192, 192, 1)' 
                        : 'rgba(255, 99, 132, 1)';
                }, $att_data)); ?>,
                borderWidth: 1
            }]
        },
        options: {
            animation: {
                duration: 1500,
                easing: 'easeOutBounce'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Attendance %' }
                }
            }
        }
    });
}

function buildGpaChart() {
    new Chart(document.getElementById('gpaChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($gpa_labels); ?>,
            datasets: [{
                label: 'Avg GPA',
                data: <?php echo json_encode($gpa_data); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'GPA' }
                }
            }
        }
    });
}

function buildFeesChart() {
    new Chart(document.getElementById('feesBarChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($fee_labels); ?>,
            datasets: [
                {
                    label: 'Paid Students',
                    data: <?php echo json_encode($fee_paid); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Unpaid Students',
                    data: <?php echo json_encode($fee_unpaid); ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.8)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            animation: {
                duration: 1500,
                easing: 'easeOutQuad'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Students' }
                }
            }
        }
    });
}

function buildEvalChart() {
    new Chart(document.getElementById('evalChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($eval_labels); ?>,
            datasets: [{
                label: 'Average Rating',
                data: <?php echo json_encode($eval_data); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            animation: {
                duration: 1500,
                easing: 'easeOutCubic'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    title: { display: true, text: 'Avg Rating (1–5)' }
                }
            }
        }
    });
}

// IntersectionObserver to animate on scroll
document.addEventListener('DOMContentLoaded', function () {
    const triggers = document.querySelectorAll('.chart-trigger');

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const canvas = entry.target.querySelector('canvas');
                if (canvas) {
                    switch (canvas.id) {
                        case 'attendanceChart': buildAttendanceChart(); break;
                        case 'gpaChart': buildGpaChart(); break;
                        case 'feesBarChart': buildFeesChart(); break;
                        case 'evalChart': buildEvalChart(); break;
                    }
                    entry.target.classList.add('chart-visible');
                    obs.unobserve(entry.target);
                }
            }
        });
    }, { threshold: 0.5 });

    triggers.forEach(el => observer.observe(el));
});
</script>

<!-- Add fade up CSS -->
<style>
.chart-trigger {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}
.chart-visible {
    opacity: 1 !important;
    transform: translateY(0) !important;
}
</style>

<?php include '../includes/footer.php'; ?>
