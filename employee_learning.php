<?php
session_start();
include 'db.php';

// Simulate logged-in employee (remove if you already use $_SESSION['user'])
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2; // Example employee id
}
$user_id = $_SESSION['user_id'];

// Fetch courses with progress for the logged-in employee
$sql = "
    SELECT c.*, 
           tp.completion 
    FROM courses c
    LEFT JOIN training_progress tp 
        ON c.id = tp.course_id AND tp.user_id = ?
    WHERE c.status='active'
    ORDER BY c.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$courses = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Learning History</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
body {
  font-family: Arial, sans-serif;
  background: #f8f9fa;
  margin: 0;
  display: flex;
}

/* ===== Sidebar ===== */
.sidebar {
  width: 250px;
  background: #2b3a67;
  color: #fff;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  padding-top: 60px;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, width 0.3s ease;
  box-shadow: 2px 0 10px rgba(0,0,0,0.3);
  z-index: 1000;
}
.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
}
.sidebar ul li a {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #fff;
  text-decoration: none;
  font-size: 15px;
  transition: background 0.3s, padding 0.3s;
}
.sidebar ul li a i {
  margin-right: 12px;
  min-width: 20px;
  text-align: center;
}
.sidebar ul li a span {
  white-space: nowrap;
}
.sidebar ul li a:hover {
  background: #3d4f8b;
}

/* Collapsed mode (desktop) */
.sidebar.collapsed {
  width: 70px;
}
.sidebar.collapsed ul li a span {
  display: none;
}

/* ===== Toggle Button ===== */
.toggle-btn {
  position: absolute;
  top: 10px;
  right: -22px;
  background: #2b3a67;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #fff;
  border: 2px solid #fff;
  font-size: 18px;
  transition: background 0.3s, transform 0.2s;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  z-index: 1100;
}
.toggle-btn:hover {
  background: #3d4f8b;
  transform: scale(1.1);
}

/* ===== Main Content ===== */
.main-content {
  margin-left: 250px;
  padding: 30px;
  width: calc(100% - 250px);
  transition: margin-left 0.3s ease, width 0.3s ease;
}
.main-content.collapsed {
  margin-left: 70px;
  width: calc(100% - 70px);
}

/* ===== Header ===== */
.header {
  text-align: center;
  margin-bottom: 20px;
}
.header h2 {
  color: #333;
  margin: 0;
}
.header p {
  color: #666;
  font-size: 14px;
}

/* ===== Course Card ===== */
.course-card {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}
.course-card h3 {
  margin: 0 0 10px;
}
.tag {
  background: #e9ecef;
  border-radius: 20px;
  padding: 5px 10px;
  font-size: 12px;
  display: inline-block;
  margin-bottom: 10px;
}
.meta {
  font-size: 14px;
  color: #555;
  margin-top: 5px;
}
.btn {
  display: inline-block;
  margin: 10px 5px 0 0;
  padding: 8px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  text-decoration: none;
}
.btn-green {
  background: #28a745;
  color: #fff;
}
.btn-blue {
  background: #007bff;
  color: #fff;
}

/* Progress Bar */
.progress-bar-container {
  background: #e9ecef;
  border-radius: 8px;
  height: 18px;
  width: 100%;
  margin-top: 10px;
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  border-radius: 8px;
  text-align: center;
  color: white;
  font-size: 12px;
  line-height: 18px;
  background: #28a745;
  width: 0%;
  transition: width 0.5s ease;
}

/* ===== Mobile View ===== */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    width: 250px;
  }
  .sidebar.active {
    transform: translateX(0);
  }
  .sidebar.collapsed {
    width: 250px; /* prevent squished mobile sidebar */
  }
  .main-content {
    margin-left: 0;
    width: 100%;
  }
  .main-content.collapsed {
    margin-left: 0;
    width: 100%;
  }
  .toggle-btn {
    left: 15px;
    right: auto;
    top: 15px;
    border: 2px solid #2b3a67;
    background: #2b3a67;
  }
}


/* ===== Main Content ===== */
.main-content {
  margin-left: 250px;
  padding: 30px;
  width: calc(100% - 250px);
  transition: margin-left 0.3s ease, width 0.3s ease;
}
.main-content.collapsed {
  margin-left: 70px;
  width: calc(100% - 70px);
}

/* ===== Header ===== */
.header {
  text-align: center;
  margin-bottom: 20px;
}
.header h2 {
  color: #333;
  margin: 0;
}
.header p {
  color: #666;
  font-size: 14px;
}

/* ===== Course Card ===== */
.course-card {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}
.course-card h3 {
  margin: 0 0 10px;
}
.tag {
  background: #e9ecef;
  border-radius: 20px;
  padding: 5px 10px;
  font-size: 12px;
  display: inline-block;
  margin-bottom: 10px;
}
.meta {
  font-size: 14px;
  color: #555;
  margin-top: 5px;
}
.btn {
  display: inline-block;
  margin: 10px 5px 0 0;
  padding: 8px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  text-decoration: none;
}
.btn-green {
  background: #28a745;
  color: #fff;
}
.btn-blue {
  background: #007bff;
  color: #fff;
}

/* Progress Bar */
.progress-bar-container {
  background: #e9ecef;
  border-radius: 8px;
  height: 18px;
  width: 100%;
  margin-top: 10px;
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  border-radius: 8px;
  text-align: center;
  color: white;
  font-size: 12px;
  line-height: 18px;
  background: #28a745;
  width: 0%;
  transition: width 0.5s ease;
}

  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <ul>
         <li><a href="employee_dashboard.php"><i class="fas fa-home"></i> <span>My Dashboard</span></a></li>
            <li><a href="employee_Profile.php"><i class="fas fa-user-check"></i> <span>Competency Profile</span></a></li>
            <li><a href="employee_Learning.php"><i class="fas fa-book"></i> <span>My Learning</span></a></li>
            <li><a href="employee_Trainings.php"><i class="fas fa-chalkboard-teacher"></i> <span>My Trainings</span></a></li>
            <li><a href="employee_Development.php"><i class="fas fa-briefcase"></i> <span>My Career Development</span></a></li>
            <li><a href="employee_Personal.php"><i class="fas fa-id-badge"></i> <span>Personal Profile</span></a></li>
    </ul>
    <span class="toggle-btn" id="toggle-sidebar"><i class="fas fa-bars"></i></span>
    </div>

<div class="main-content" id="main-content">
    <div class="header">
      <h2>📘 Learning History</h2>
      <p>Your comprehensive learning journey and achievements</p>
    </div>

    <?php while ($row = $courses->fetch_assoc()) { 
      $progress = $row['completion'] ?? 0;
    ?>
      <div class="course-card">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        <span class="tag"><?php echo htmlspecialchars($row['tags']); ?></span>
        <p><?php echo htmlspecialchars($row['description']); ?></p>

        <div class="meta">
          <i class="fa-regular fa-clock"></i> Duration: <?php echo $row['duration'] ? htmlspecialchars($row['duration']) : 'N/A'; ?><br>
          <i class="fa-solid fa-signal"></i> Level: <?php echo $row['level'] ? htmlspecialchars($row['level']) : 'N/A'; ?><br>
          <i class="fa-regular fa-calendar-check"></i> Completed: 
          <?php echo $row['completed_date'] ? htmlspecialchars($row['completed_date']) : 'In Progress'; ?>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar-container">
          <div class="progress-bar" style="width: <?php echo $progress; ?>%;">
            <?php echo $progress; ?>%
          </div>
        </div>

        <?php if ($row['file_name']) { ?>
          <a href="uploads/<?php echo htmlspecialchars($row['file_name']); ?>" target="_blank" class="btn btn-green">
            <i class="fa-solid fa-download"></i> Download Certificate
          </a>
        <?php } ?>
        
        <?php if ($row['link_url']) { ?>
          <a href="<?php echo htmlspecialchars($row['link_url']); ?>" target="_blank" class="btn btn-blue">
            <i class="fa-solid fa-book-open"></i> Review Course
          </a>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
  <script>
const sidebar = document.getElementById("sidebar");
const mainContent = document.getElementById("main-content");
const toggleBtn = document.getElementById("toggle-sidebar");

toggleBtn.onclick = function() {
  if (window.innerWidth <= 768) {
    // Mobile: slide sidebar
    sidebar.classList.toggle("active");
  } else {
    // Desktop: collapse sidebar
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("collapsed");
  }
};

</script>
</body>
</html>
