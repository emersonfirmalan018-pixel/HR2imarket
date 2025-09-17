<?php
session_start();
include 'db.php';

// Check if admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php?role=admin");
    exit();
}

$id = intval($_GET['id']);

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='employee'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Update user info
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $email, $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Error updating user.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Employee</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
  <h2>Edit Employee</h2>
  <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
  <form method="POST">
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
    <button type="submit">Update</button>
  </form>
  <br>
  <a href="admin_dashboard.php" class="btn">Back</a>
</div>
</body>
</html>
