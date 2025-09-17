<?php
session_start();
include 'db.php';

// Ensure employee is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// =====================
// Training Stats
// =====================
$total_trainings = $conn->query("SELECT COUNT(*) as c FROM trainings")->fetch_assoc()['c'];
$completed = $conn->query("SELECT COUNT(*) as c FROM training_progress WHERE user_id=$user_id AND completion=100")->fetch_assoc()['c'];
$upcoming = $conn->query("SELECT COUNT(*) as c FROM trainings WHERE training_date >= CURDATE()")->fetch_assoc()['c'];
$certificates = $completed; // you can later create separate certificate table

// Training hours (sum of completed durations)
$training_hours = $conn->query("
    SELECT IFNULL(SUM(t.duration),0) as hrs
    FROM training_progress tp
    JOIN courses c ON tp.course_id=c.id
    JOIN trainings t ON t.title=c.title
    WHERE tp.user_id=$user_id AND tp.completion=100
")->fetch_assoc()['hrs'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Trainings</title>
  <!-- ✅ Font Awesome (Icons Fix) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f6fa;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 90%;
      margin: 20px auto;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .header h2 {
      margin: 0;
    }
    .header button {
      background: #4a6cf7;
      color: #fff;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
    }
    .tabs {
      margin-top: 20px;
      border-bottom: 1px solid #ccc;
      display: flex;
    }
    .tab {
      padding: 10px 20px;
      cursor: pointer;
    }
    .tab.active {
      border-bottom: 3px solid #4a6cf7;
      font-weight: bold;
    }
    .overview {
      margin-top: 20px;
    }
    .card {
      display: inline-block;
      width: 180px;
      background: #fff;
      padding: 20px;
      margin: 10px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    .card h3 {
      margin: 0;
      font-size: 28px;
      color: #4a6cf7;
    }
    .card p {
      margin: 5px 0 0;
      color: #666;
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
  padding: 20px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: width 0.3s;
  overflow: hidden;
  box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}
.sidebar.collapsed {
  width: 70px;
}
.sidebar ul {
  list-style: none;
  width: 100%;
  padding: 0;
  margin: 0;
}
.sidebar ul li {
  position: relative;
}
.sidebar ul li a {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #fff;
  text-decoration: none;
  transition: background 0.3s;
  font-size: 15px;
}
.sidebar ul li a i {
  margin-right: 12px;
  min-width: 20px;
  text-align: center;
}
.sidebar.collapsed ul li a span {
  display: none;
}
.sidebar ul li a:hover {
  background: #3d4f8b;
}

/* Toggle Button */
.toggle-btn {
  position: absolute;
  top: 20px;
  right: -25px;
  background: #2b3a67;
  border-radius: 50%;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #fff;
  border: 2px solid #fff;
  font-size: 20px;
  transition: background 0.3s, transform 0.2s;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.toggle-btn:hover {
  background: #3d4f8b;
  transform: scale(1.1);
}

/* ===== Main Content ===== */
.main-content {
  margin-left: 250px;
  padding: 20px;
  width: calc(100% - 250px);
  transition: margin-left 0.3s, width 0.3s;
}
.main-content.collapsed {
  margin-left: 70px;
  width: calc(100% - 70px);
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
      <h2>My Trainings</h2>
      <div>
        <button onclick="location.reload()"><i class="fa fa-sync"></i> Refresh</button>
        <button><i class="fa fa-calendar"></i> View Schedule</button>
      </div>
    </div>

    <div class="tabs">
      <div class="tab active">Training Overview</div>
      <div class="tab">My Schedule</div>
      <div class="tab">Training History</div>
    </div>

    <div class="overview">
      <h2>Training Overview</h2>
      <p>Your Training Journey<br>Track your training progress and achievements</p>

      <div class="card">
        <h3><?= $total_trainings ?></h3>
        <p>Total Trainings</p>
      </div>
      <div class="card">
        <h3><?= $completed ?></h3>
        <p>Completed</p>
      </div>
      <div class="card">
        <h3><?= $upcoming ?></h3>
        <p>Upcoming</p>
      </div>
      <div class="card">
        <h3><?= $certificates ?></h3>
        <p>Certificates</p>
      </div>
      <div class="card">
        <h3><?= $training_hours ?></h3>
        <p>Training Hours</p>
      </div>
    </div>
  </div>
</body>
<script>
document.getElementById("toggle-sidebar").onclick = function() {
    document.getElementById("sidebar").classList.toggle("collapsed");
    document.getElementById("main-content").classList.toggle("collapsed");
};
</script>
</html>
