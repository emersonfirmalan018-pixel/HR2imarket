<?php 
session_start();
include 'db.php';

// Check if logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php?role=admin");
    exit();
}

// Dashboard stats
$total_employees = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role='employee'")->fetch_assoc()['count'];
$active_courses = $conn->query("SELECT COUNT(*) AS count FROM courses WHERE status='active'")->fetch_assoc()['count'] ?? 0;
$training = $conn->query("SELECT AVG(completion) AS avg_completion FROM training_progress")->fetch_assoc();
$training_completion = $training ? round($training['avg_completion'], 0) : 0;
$succession_plans = $conn->query("SELECT COUNT(*) AS count FROM succession_plans")->fetch_assoc()['count'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f6f9;
      display: flex;
    }
    /* Sidebar icons */
.sidebar ul li a i {
  margin-right: 15px;
  min-width: 25px;
  text-align: center;
  font-size: 18px;   /* uniform size */
  color: #f1f1f1;    /* softer contrast for sidebar */
}
.sidebar ul li a:hover i {
  color: #ffcc00;    /* highlight on hover */
}

/* Dashboard card icons */
.card i {
  font-size: 40px;   /* slightly bigger for emphasis */
  color: #2b3a67;
  margin-bottom: 12px;
  display: block;
}

/* Activity icons */
.activity i {
  background: #3498db;
  color: #fff;
  font-size: 16px;
  border-radius: 50%;
  width: 40px;       /* fixed width */
  height: 40px;      /* fixed height */
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
  flex-shrink: 0;
}

/* Quick Actions icons */
.quick-actions button i {
  margin-right: 12px;
  font-size: 18px;
  color: #2b3a67;
  width: 22px;       /* keep icons aligned */
  text-align: center;
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

    /* Dashboard cards */
    .dashboard {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    .card {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      text-align: center;
      transform: scale(0.9);
      opacity: 0;
      animation: fadeInUp 0.7s ease forwards;
    }
    .card:nth-child(1) { animation-delay: 0.2s; }
    .card:nth-child(2) { animation-delay: 0.4s; }
    .card:nth-child(3) { animation-delay: 0.6s; }
    .card:nth-child(4) { animation-delay: 0.8s; }

    .card i { font-size: 32px; color: #1b2a47; margin-bottom: 10px; }
    .card h3 { margin: 10px 0 5px; }
    .card p { font-size: 20px; font-weight: bold; }

    /* Hover effect */
    .card:hover {
      transform: translateY(-8px) scale(1.05);
      transition: all 0.3s ease;
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    @keyframes fadeInUp {
      from { transform: translateY(30px) scale(0.9); opacity: 0; }
      to { transform: translateY(0) scale(1); opacity: 1; }
    }

    /* ===== New Section (Recent Activities + Quick Actions) ===== */
    .two-column {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
    }
    .activities, .quick-actions {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }
    .activities h3, .quick-actions h3 {
      margin-bottom: 15px;
      border-bottom: 1px solid #eee;
      padding-bottom: 8px;
      color: #333;
    }

    /* Activity item */
    .activity {
      display: flex;
      align-items: flex-start;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 8px;
      background: #f9f9f9;
    }
    .activity i {
      background: #3498db;
      color: #fff;
      font-size: 18px;
      border-radius: 50%;
      padding: 10px;
      margin-right: 12px;
    }
    .activity strong { display: block; margin-bottom: 4px; color: #2c3e50; }
    .activity small { color: gray; }

    /* Quick Actions */
    .quick-actions button {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 10px;
      margin-bottom: 10px;
      border: none;
      border-radius: 6px;
      background: #f8f8f8;
      cursor: pointer;
      font-size: 14px;
      transition: 0.3s;
      transform: translateX(-20px);
      opacity: 0;
      animation: slideIn 0.6s ease forwards;
    }
    .quick-actions button:nth-child(1) { animation-delay: 1s; }
    .quick-actions button:nth-child(2) { animation-delay: 1.2s; }
    .quick-actions button:nth-child(3) { animation-delay: 1.4s; }
    .quick-actions button:nth-child(4) { animation-delay: 1.6s; }
    .quick-actions button:nth-child(5) { animation-delay: 1.8s; }

    .quick-actions button i {
      margin-right: 10px;
      color: #2b3a67;
      font-size: 16px;
    }
    .quick-actions button:hover {
      background: #eaeaea;
      transform: translateX(5px) scale(1.05);
    }

    @keyframes slideIn {
      from { transform: translateX(-20px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @media(max-width: 900px) {
      .two-column { grid-template-columns: 1fr; }
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

  <div class="main-content" id="main">
    <h2>Welcome Admin, <?php echo $_SESSION['user']['username']; ?> 👋</h2>

    <!-- Dashboard Cards -->
    <div class="dashboard">
      <div class="card"><i class="fas fa-users"></i><h3>Total Employees</h3><p class="counter" data-target="<?php echo $total_employees; ?>">0</p></div>
      <div class="card"><i class="fas fa-book-open"></i><h3>Active Courses</h3><p class="counter" data-target="<?php echo $active_courses; ?>">0</p></div>
      <div class="card"><i class="fas fa-chart-line"></i><h3>Training Completion</h3><p class="counter" data-target="<?php echo $training_completion; ?>">0</p>%</div>
      <div class="card"><i class="fas fa-sitemap"></i><h3>Succession Plans</h3><p class="counter" data-target="<?php echo $succession_plans; ?>">0</p></div>
    </div>

    <!-- Recent Activities + Quick Actions -->
    <div class="two-column">
      <div class="activities">
        <h3>Recent Activities</h3>
        <div class="activity">
          <i class="fas fa-user-plus"></i>
          <div>
            <strong>New Employee Added</strong>
            <span>Marian Keith joined as Customer Support</span>
            <small>2 hours ago</small>
          </div>
        </div>
        <div class="activity">
          <i class="fas fa-certificate"></i>
          <div>
            <strong>Training Completed</strong>
            <span>Julie Caspe completed "Leadership Skills"</span>
            <small>4 hours ago</small>
          </div>
        </div>
        <div class="activity">
          <i class="fas fa-clipboard-check"></i>
          <div>
            <strong>Competency Assessment</strong>
            <span>Monthly review completed for IT team</span>
            <small>1 day ago</small>
          </div>
        </div>
      </div>

      <div class="quick-actions">
        <h3>Quick Actions</h3>
        <button><i class="fas fa-chart-line"></i> Manage Competencies</button>
        <button><i class="fas fa-graduation-cap"></i> Add Course</button>
        <button><i class="fas fa-chalkboard-teacher"></i> Schedule Training</button>
        <button><i class="fas fa-project-diagram"></i> Succession Planning</button>
        <button><i class="fas fa-user-friends"></i> Employee Requests</button>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
      document.getElementById("main").classList.toggle("collapsed");
    }

    // Count-up animation
    document.querySelectorAll(".counter").forEach(counter => {
      let updateCount = () => {
        let target = +counter.getAttribute("data-target");
        let count = +counter.innerText;
        let speed = 50; // smaller = faster
        let increment = Math.ceil(target / 100);

        if (count < target) {
          counter.innerText = count + increment;
          setTimeout(updateCount, speed);
        } else {
          counter.innerText = target;
        }
      };
      updateCount();
    });
  </script>
</body>
</html>
