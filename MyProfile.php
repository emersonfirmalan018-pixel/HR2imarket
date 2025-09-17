<?php 
session_start();
include 'db.php';

// Check if logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user']['id'];
$message = "";

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Change Password
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$new_password' WHERE id=$admin_id");
        $message = "Password updated successfully!";
    }

    // Update ID Number
    if (!empty($_POST['id_number'])) {
        $id_number = $conn->real_escape_string($_POST['id_number']);
        $conn->query("UPDATE users SET id_number='$id_number' WHERE id=$admin_id");
        $message = "ID number updated successfully!";
    }

    // Upload Photo
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
            $conn->query("UPDATE users SET photo='$fileName' WHERE id=$admin_id");
            $message = "Profile photo updated successfully!";
        } else {
            $message = "Error uploading photo.";
        }
    }
}

// Fetch admin info
$result = $conn->query("SELECT * FROM users WHERE id=$admin_id");
$admin = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Admin</title>
    <!-- ✅ Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input[type="text"], input[type="password"], input[type="file"] {
            width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;
        }
        .btn {
            display: inline-block; padding: 12px; background: #2980b9;
            color: white; border: none; border-radius: 6px;
            cursor: pointer; width: 100%; font-size: 16px;
        }
        .btn:hover { background: #1c5980; }
        .profile-pic { text-align: center; margin-bottom: 15px; }
        .profile-pic img {
            width: 120px; height: 120px; border-radius: 50%;
            border: 3px solid #2980b9; object-fit: cover;
        }
        .message {
            text-align: center; margin-bottom: 15px;
            color: green; font-weight: bold;
        }
        /* ===== Sidebar ===== */
        .sidebar {
            width: 250px; background: #2b3a67; color: #fff;
            height: 100vh; position: fixed; left: 0; top: 0;
            padding: 20px 0; display: flex; flex-direction: column;
            align-items: center; transition: width 0.3s; overflow: hidden;
        }
        .sidebar.collapsed { width: 70px; }
        .sidebar h2 { font-size: 20px; margin-bottom: 20px; transition: opacity 0.3s; }
        .sidebar.collapsed h2 { opacity: 0; pointer-events: none; }
        .sidebar ul { list-style: none; width: 100%; padding: 0; }
        .sidebar ul li a {
            display: flex; align-items: center; padding: 12px 20px;
            color: #fff; text-decoration: none; transition: 0.3s;
        }
        .sidebar ul li a i { margin-right: 12px; min-width: 20px; text-align: center; }
        .sidebar.collapsed ul li a span { display: none; }
        .sidebar ul li a:hover { background: #3d4f8b; }
        /* Toggle Button */
        .toggle-btn {
            position: absolute; top: 20px; right: -20px;
            background: #2b3a67; border-radius: 50%;
            padding: 10px; cursor: pointer; color: #fff;
            border: 2px solid #fff; transition: 0.3s;
        }
        .toggle-btn:hover { background: #3d4f8b; }
        /* Main content */
        .main-content {
            margin-left: 250px; padding: 20px;
            width: calc(100% - 250px); transition: margin-left 0.3s, width 0.3s;
        }
        .main-content.collapsed { margin-left: 70px; width: calc(100% - 70px); }
    </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <h2>IMARKET</h2>
    <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
    <ul>
      <li><a href="admin_dashboard.php"><i class="fas fa-home"></i><span> My Dashboard</span></a></li>
      <li><a href="MyProfile.php"><i class="fas fa-user"></i><span> Profile</span></a></li>
      <li><a href="MyLearning.php"><i class="fas fa-book"></i><span> My Learning</span></a></li>
      <li><a href="MyTrainings.php"><i class="fas fa-chalkboard-teacher"></i><span> My Trainings</span></a></li>
      <li><a href="Succession.php"><i class="fas fa-briefcase"></i><span> Succession</span></a></li>
      <li><a href="ESS.php"><i class="fas fa-id-badge"></i><span> Employee Self-Service</span></a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
    </ul>
  </div>

  <div class="main-content" id="main">
    <h2>My Profile</h2>

    <?php if ($message) echo "<p class='message'>$message</p>"; ?>

    <div class="profile-pic">
        <?php if (!empty($admin['photo'])): ?>
            <img id="profilePreview" src="uploads/<?php echo $admin['photo']; ?>" alt="Profile Photo">
        <?php else: ?>
            <img id="profilePreview" src="https://via.placeholder.com/120" alt="Default Photo">
        <?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>ID Number</label>
            <input type="text" name="id_number" value="<?php echo htmlspecialchars($admin['id_number'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password">
        </div>

        <div class="form-group">
            <label>Upload Photo</label>
            <input type="file" name="photo" id="photoInput" accept="image/*">
        </div>

        <button type="submit" class="btn">Update Profile</button>
    </form>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
      document.getElementById("main").classList.toggle("collapsed");
    }

    // ✅ Live preview of profile photo before upload
    document.getElementById("photoInput").addEventListener("change", function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById("profilePreview").src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
