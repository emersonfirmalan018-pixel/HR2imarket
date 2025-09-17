<?php 
session_start();
include 'db.php';

// Handle Course Insert
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_course'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $link_url = $_POST['link_url'];
    $tags = $_POST['tags'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $level = $_POST['level'];
    $completed_date = $_POST['completed_date'];

    // File upload
    $file_name = null;
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['size'] > 0) {
        if ($_FILES['upload_file']['size'] <= 1048576) { // 1MB
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_name = time() . "_" . basename($_FILES["upload_file"]["name"]);
            $target_file = $target_dir . $file_name;
            move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file);
        } else {
            echo "<script>alert('File too large! Max 1MB.');</script>";
        }
    }

    $stmt = $conn->prepare("INSERT INTO courses 
        (title, type, link_url, file_name, tags, description, duration, level, completed_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("sssssssss", $title, $type, $link_url, $file_name, $tags, $description, $duration, $level, $completed_date);
    $stmt->execute();
    $stmt->close();
    header("Location: MyLearning.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM courses WHERE id=$id");
    header("Location: MyLearning.php");
    exit();
}

// Fetch Courses
$courses = $conn->query("SELECT * FROM courses ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Learning Management - My Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f9; }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #2b3a67;
      color: #fff;
      height: 100vh;
      position: fixed;
      left: 0; top: 0;
      padding: 20px 0;
      display: flex; flex-direction: column; align-items: center;
      transition: width 0.3s;
      overflow: hidden;
      z-index: 1000;
    }
    .sidebar.collapsed { width: 70px; }
    .sidebar h2 { font-size: 20px; margin-bottom: 20px; transition: opacity 0.3s; }
    .sidebar.collapsed h2 { opacity: 0; pointer-events: none; }
    .sidebar ul { list-style: none; padding: 0; width: 100%; }
    .sidebar ul li a {
      display: flex; align-items: center; padding: 12px 20px;
      color: #fff; text-decoration: none; transition: 0.3s;
    }
    .sidebar ul li a i { margin-right: 12px; font-size: 18px; }
    .sidebar.collapsed ul li a span { display: none; }
    .sidebar ul li a:hover { background: #3d4f8b; }
    .toggle-btn {
      position: absolute; top: 20px; right: -15px;
      background: #2b3a67; border-radius: 50%; padding: 8px;
      cursor: pointer; color: #fff; border: 2px solid #fff; font-size: 14px;
    }

    /* Main Content */
    .main-content {
      margin-left: 250px;
      padding: 30px;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }
    .sidebar.collapsed ~ .main-content { margin-left: 70px; }

    .content-wrapper {
      max-width: 1200px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .header { background: #0099e6; padding: 15px; color: #fff; border-radius: 10px; text-align: center; }
    .tabs { display: flex; gap: 10px; margin: 15px 0; border-bottom: 1px solid #ccc; }
    .tabs a { padding: 10px 20px; text-decoration: none; color: #333; }
    .tabs a.active { border-bottom: 3px solid #0099e6; font-weight: bold; }

    /* Flex Layout */
    .flex { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; }
    .form-card, .table-card {
      flex: 1;
      min-width: 350px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    .form-card input, .form-card select, .form-card textarea {
      width: 100%; padding: 8px; margin: 6px 0;
      border: 1px solid #ccc; border-radius: 5px;
    }
    .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; }
    .btn-primary { background: #0099e6; color: #fff; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-secondary { background: #6c757d; color: #fff; }

   /* ===== Table Styling ===== */
.table-card h3 {
  margin-bottom: 15px;
  font-size: 20px;
  color: #2b3a67;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

thead {
  background: #0099e6;
  color: #fff;
  text-align: left;
}

th, td {
  padding: 12px 15px;
  font-size: 14px;
}

tbody tr:nth-child(even) {
  background: #f8f9fa;
}

tbody tr:hover {
  background: #e6f3ff;
  transition: 0.2s ease-in-out;
}

td a {
  color: #0099e6;
  text-decoration: none;
  font-weight: 500;
}

td a:hover {
  text-decoration: underline;
}

.actions .btn {
  padding: 6px 10px;
  font-size: 13px;
  margin: 2px 0;
}

/* Make table responsive */
.table-card {
  overflow-x: auto;
}

  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <h2>IMARKET</h2>
    <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
    <ul>
      <li><a href="admin_dashboard.php"><i class="fas fa-home"></i><span> My Dashboard</span></a></li>
      <li><a href="MyProfile.php"><i class="fas fa-user-check"></i><span>Profile</span></a></li>
      <li><a href="MyLearning.php"><i class="fas fa-book"></i><span> My Learning</span></a></li>
      <li><a href="MyTrainings.php"><i class="fas fa-chalkboard-teacher"></i><span> My Trainings</span></a></li>
      <li><a href="Succession.php"><i class="fas fa-briefcase"></i><span> Succession</span></a></li>
      <li><a href="ESS.php"><i class="fas fa-id-badge"></i><span> Employee Self-Service</span></a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="content-wrapper">
      <div class="header"><h2>Learning Management</h2></div>

      <div class="tabs">
        <a href="MyLearning.php" class="active">Course Library</a>
        <a href="#">Course Assignment</a>
        <a href="#">Completion Tracking</a>
        <a href="#">Backup</a>
      </div>

      <div class="flex">
        <!-- Form -->
        <form class="form-card" method="post" enctype="multipart/form-data">
          <label>Title</label>
          <input type="text" name="title" required>

          <label>Type</label>
          <select name="type">
              <option>PDF</option>
              <option>Video</option>
              <option>Module</option>
          </select>

          <label>Link URL</label>
          <input type="url" name="link_url">

          <label>Upload File (<=1MB)</label>
          <input type="file" name="upload_file">

          <label>Tags</label>
          <input type="text" name="tags">

          <label>Description</label>
          <textarea name="description"></textarea>

          <label>Duration</label>
          <input type="text" name="duration" placeholder="e.g. 6 hours">

          <label>Level</label>
          <select name="level">
              <option>Beginner</option>
              <option>Intermediate</option>
              <option>Advanced</option>
          </select>

          <label>Completed Date</label>
          <input type="date" name="completed_date">

          <button type="submit" name="save_course" class="btn btn-primary">Save Course</button>
          <button type="reset" class="btn btn-secondary">Reset</button>
        </form>

        <!-- Table -->
        <div class="table-card">
          <h3>Available Courses</h3>
          <table>
            <thead>
              <tr>
                <th>Title</th><th>Type</th><th>Tags</th><th>File / Link</th><th>Description</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $courses->fetch_assoc()) { ?>
              <tr>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= htmlspecialchars($row['type']); ?></td>
                <td>
                  <?php foreach (explode(",", $row['tags']) as $tag) {
                      echo "<span class='tag'>" . htmlspecialchars(trim($tag)) . "</span>";
                  } ?>
                </td>
                <td>
                  <?php if ($row['file_name']) { ?>
                    <a href="uploads/<?= htmlspecialchars($row['file_name']); ?>" target="_blank">Download</a>
                  <?php } elseif ($row['link_url']) { ?>
                    <a href="<?= htmlspecialchars($row['link_url']); ?>" target="_blank">Open Link</a>
                  <?php } else { echo "-"; } ?>
                </td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td class="actions">
                  <a href="MyLearning.php?delete=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this course?');">Delete</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
    }
  </script>
</body>
</html>
