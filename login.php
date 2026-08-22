<?php
session_start();
include 'includes/db.php';

$error = "";

// Process login
if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if($user['role'] == 'admin'){
            header("Location: admin/dashboard.php");
        } elseif($user['role'] == 'teacher'){
            header("Location: teacher/dashboard.php");
        } elseif($user['role'] == 'student'){
            header("Location: student/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Portal Login</title>

    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h2>University Portal</h2>
    <p class="subtitle">Welcome — Please Login</p>

    <?php if(!empty($error)){ ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <form method="post" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
