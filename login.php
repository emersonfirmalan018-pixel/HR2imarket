<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role=?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        if ($role == "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="form-container">
  <!-- Company Logo -->
  <div class="company-header">
    <img src="logo.png" alt="iMarket Logo" class="logo">
  </div>

  <!-- Role Icon -->
  <div class="role-icon">
    <?php if(($_GET['role'] ?? '') == 'admin'): ?>
        <i class="fas fa-user-tie"></i>
    <?php else: ?>
        <i class="fas fa-user"></i>
    <?php endif; ?>
  </div>

  <h2>Login as <?php echo ucfirst($_GET['role'] ?? ''); ?></h2>
  <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
  
  <form method="POST">
    <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>

  <div class="form-links">
    <?php if(($_GET['role'] ?? '') == 'admin'): ?>
        <a href="login.php?role=employee">Login as Employee</a>
    <?php else: ?>
        <a href="login.php?role=admin">Login as Admin</a>
    <?php endif; ?>
    <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>

</body>
</html>
