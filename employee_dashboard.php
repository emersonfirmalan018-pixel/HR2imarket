<?php 
session_start(); 
if(!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit(); 
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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

    .sidebar .profile {
      text-align: center;
      margin-bottom: 20px;
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
      transition: opacity 0.3s;
    }
    .sidebar.collapsed h3 {
      opacity: 0;
      pointer-events: none;
    }

    .sidebar ul {
      list-style: none;
      width: 100%;
      padding: 0;
      margin: 0;
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
      right: -15px;
      background: #2b3a67;
      border-radius: 50%;
      padding: 10px;
      cursor: pointer;
      color: #fff;
      border: 2px solid #fff;
      transition: background 0.3s;
    }
    .toggle-btn:hover {
      background: #3d4f8b;
    }

    /* Main content */
    .main-content {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s, width 0.3s;
    }
    .main-content.collapsed {
      margin-left: 70px;
    }

    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .logout-btn {
      background: #e74c3c;
      color: #fff;
      padding: 8px 15px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.3s;
    }
    .logout-btn:hover {
      background: #c0392b;
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="profile">
            <img src="user.png" alt="Employee" class="profile-pic">
            <h3><?php echo $_SESSION['user']['username']; ?></h3>
        </div>
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

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="header">
            <h1>Welcome Employee <?php echo $_SESSION['user']['username']; ?>!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-icon icon-purple"><i class="fas fa-chart-line"></i></div>
                <h3>My Competency Score</h3>
                <p>85%</p>
                <small>+5% from last month</small>
            </div>
            <div class="card">
                <div class="card-icon icon-orange"><i class="fas fa-tasks"></i></div>
                <h3>Pending Requests</h3>
                <p>3</p>
                <small>2 require attention</small>
            </div>
            <div class="card">
                <div class="card-icon icon-pink"><i class="fas fa-graduation-cap"></i></div>
                <h3>Learning Progress</h3>
                <p>72%</p>
                <small>3 courses in progress</small>
            </div>
            <div class="card">
                <div class="card-icon icon-blue"><i class="fas fa-route"></i></div>
                <h3>Career Path</h3>
                <p>Level 3</p>
                <small>Next: Senior Developer</small>
            </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="upcoming-tasks">
            <h2>Upcoming Tasks</h2>
            <ul>
                <li>Complete Competency Assessment <span class="due">Due: Tomorrow</span></li>
            </ul>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <ul>
                <li>✅ Completed "React Fundamentals" course (2 hours ago)</li>
                <li>✏️ Updated personal information in ESS (1 day ago)</li>
                <li>📊 Competency assessment score improved (3 days ago)</li>
                <li>⭐ Received positive feedback from manager (1 week ago)</li>
            </ul>
        </div>
    </div>

     <script>
      document.getElementById("toggle-sidebar").addEventListener("click", function() {
        document.getElementById("sidebar").classList.toggle("collapsed");
        document.getElementById("main-content").classList.toggle("collapsed");
      });
    </script>
</body>
</html>
