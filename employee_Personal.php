<?php
session_start();
include 'db.php';

// Check if logged in as employee
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['user']['id'];

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];

    $photo_sql = "";
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath);
        $photo_sql = ", photo='$fileName'";
    }

    $conn->query("UPDATE users 
                  SET fullname='$fullname', email='$email', contact='$contact', address='$address', birthday='$birthday' $photo_sql
                  WHERE id='$employee_id'");

    $_SESSION['message'] = "Profile updated successfully!";
    header("Location: employee_Personal.php");
    exit();
}

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $result = $conn->query("SELECT password FROM users WHERE id='$employee_id'");
    $row = $result->fetch_assoc();

    if (password_verify($current_password, $row['password'])) {
        $conn->query("UPDATE users SET password='$new_password' WHERE id='$employee_id'");
        $_SESSION['message'] = "Password changed successfully!";
    } else {
        $_SESSION['error'] = "Current password is incorrect!";
    }
    header("Location: employee_Personal.php");
    exit();
}

// Fetch Employee Data
$user = $conn->query("SELECT * FROM users WHERE id='$employee_id'")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Personal Profile</title>
  <link rel="stylesheet" href="style.css">

  <!-- ✅ Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    h2 { color: #333; }
    input, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
    button { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; transition: 0.3s; }
    button:hover { background: #0056b3; transform: scale(1.05); }
    .profile-photo { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; }
    .message { padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }

    /* ===== Sidebar ===== */
    .sidebar {
      width: 250px;
      background: #2b3a67;
      color: #fff;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: width 0.4s ease;
      overflow: hidden;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
    }
    .sidebar.collapsed { width: 70px; }

    .sidebar .profile {
      text-align: center;
      margin-bottom: 20px;
      transition: opacity 0.4s ease;
    }
    .sidebar .profile-pic {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
      border: 2px solid #fff;
    }
    .sidebar h3 {
      font-size: 16px;
      margin: 0;
      transition: opacity 0.3s ease;
    }
    .sidebar.collapsed h3 { opacity: 0; pointer-events: none; }

    .sidebar ul {
      list-style: none;
      width: 100%;
      padding: 0;
      margin: 0;
    }
    .sidebar ul li { width: 100%; }
    .sidebar ul li a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: #fff;
      text-decoration: none;
      transition: background 0.3s ease, padding 0.3s ease;
      font-size: 15px;
    }
    .sidebar ul li a i {
      font-size: 18px;
      width: 25px;
      text-align: center;
      margin-right: 10px;
      transition: margin 0.3s ease;
    }
    .sidebar ul li a span {
      flex: 1;
      opacity: 1;
      transition: opacity 0.3s ease;
    }
    .sidebar.collapsed ul li a span { opacity: 0; pointer-events: none; }
    .sidebar ul li a:hover { background: #3d4f8b; }

    /* Toggle Button */
    .toggle-btn {
      position: absolute;
      top: 20px;
      right: -15px;
      background: #2b3a67;
      border-radius: 50%;
      padding: 10px;
      cursor: pointer;
      color: #fff;
      border: 2px solid #fff;
      transition: background 0.3s, transform 0.3s;
    }
    .toggle-btn:hover { background: #3d4f8b; transform: rotate(90deg); }

    /* Main content */
    .main-content {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.4s ease, width 0.4s ease;
    }
    .main-content.collapsed { margin-left: 70px; }
  </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="profile">
            <img src="uploads/<?php echo $user['photo'] ?? 'default.png'; ?>" class="profile-pic">
            <h3><?php echo $user['fullname']; ?></h3>
        </div>
        <ul>
            <li><a href="employee_dashboard.php"><i class="fas fa-home"></i><span>My Dashboard</span></a></li>
            <li><a href="employee_Profile.php"><i class="fas fa-user-check"></i><span>Competency Profile</span></a></li>
            <li><a href="employee_Learning.php"><i class="fas fa-book"></i><span>My Learning</span></a></li>
            <li><a href="employee_Trainings.php"><i class="fas fa-chalkboard-teacher"></i><span>My Trainings</span></a></li>
            <li><a href="employee_Development.php"><i class="fas fa-briefcase"></i><span>My Career Development</span></a></li>
            <li><a href="employee_Personal.php"><i class="fas fa-id-badge"></i><span>Personal Profile</span></a></li>
        </ul>
        <span class="toggle-btn" id="toggle-sidebar"><i class="fas fa-bars"></i></span>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <h2>My Personal Profile</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <h3>Profile Information</h3>
        <form method="post" enctype="multipart/form-data">
            <img src="uploads/<?php echo $user['photo'] ?? 'default.png'; ?>" class="profile-photo"><br>
            <label>Change Photo:</label>
            <input type="file" name="photo">
            <label>Full Name:</label>
            <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            <label>Contact:</label>
            <input type="text" name="contact" value="<?php echo $user['contact']; ?>">
            <label>Address:</label>
            <textarea name="address"><?php echo $user['address']; ?></textarea>
            <label>Birthday:</label>
            <input type="date" name="birthday" value="<?php echo $user['birthday']; ?>">
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <h3>Change Password</h3>
        <form method="post">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>

    <script>
      document.getElementById("toggle-sidebar").addEventListener("click", function() {
        document.getElementById("sidebar").classList.toggle("collapsed");
        document.getElementById("main-content").classList.toggle("collapsed");
      });
    </script>
</body>
</html>
