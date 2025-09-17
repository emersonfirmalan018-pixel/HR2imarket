<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Career Development & Succession Planning</title>
  <!-- ✅ Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    header {
      padding: 20px;
      font-size: 22px;
      font-weight: bold;
      color: #333;
      background: #fff;
      border-bottom: 1px solid #ddd;
    }
    header button {
      float: right;
      padding: 8px 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      cursor: pointer;
      background: #f1f1f1;
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
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
      z-index: 1000;
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
      transition: margin-left 0.3s;
    }
    .main-content.collapsed {
      margin-left: 70px;
    }

    /* Tabs */
    nav {
      display: flex;
      border-bottom: 1px solid #ddd;
      background: #fff;
      margin-left: 250px;
      transition: margin-left 0.3s;
    }
    nav.collapsed {
      margin-left: 70px;
    }
    nav button {
      flex: 1;
      padding: 14px;
      border: none;
      background: #fff;
      font-size: 16px;
      cursor: pointer;
    }
    nav button.active {
      border-bottom: 3px solid #333;
      font-weight: bold;
    }

    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      color: #666;
      padding: 15px;
    }

    /* ✅ Responsive Sidebar */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }
      .sidebar.collapsed {
        width: 0;
      }
      nav {
        margin-left: 70px;
      }
      .main-content {
        margin-left: 70px;
      }
    }

    /* ✅ Animation for Development Plan */
    .fade-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp 0.8s ease forwards;
    }
    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<header>
  My Career Development & Succession Planning
  <button>Refresh Data</button>
</header>

<!-- Sidebar -->
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

<!-- Navigation Tabs -->
<nav id="nav-tabs">
  <button class="tab-btn active" data-tab="opportunities">My Opportunities</button>
  <button class="tab-btn" data-tab="development">Development Plan</button>
  <button class="tab-btn" data-tab="pathways">Career Pathways</button>
  <button class="tab-btn" data-tab="readiness">My Readiness</button>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">

  <!-- My Opportunities -->
  <div id="opportunities" class="tab-content active">
    <h2>My Succession Opportunities</h2>
    <p><b>No Current Opportunities</b></p>
    <p>Continue developing your skills</p>
  </div>

  <!-- Development Plan (Animated) -->
  <div id="development" class="tab-content">
    <h2>My Development Plan</h2>

    <div class="fade-card" style="animation-delay:0.1s">
      <h3>🎯 Skills to Develop</h3>
      <p>Leadership & Management – 45%</p>
      <p>Strategic Planning – 30%</p>
      <p>Budget Management – 20%</p>
      <p>Team Building – 60%</p>
    </div>

    <div class="fade-card" style="animation-delay:0.3s">
      <h3>📘 Recommended Training</h3>
      <ul>
        <li>Leadership Fundamentals (2 weeks – Available)</li>
        <li>Project Management Certification (6 weeks – Enrolled)</li>
        <li>Financial Management for Non-Financial Managers (3 weeks – Available)</li>
        <li>Advanced Communication Skills (1 week – Available)</li>
      </ul>
    </div>

    <div class="fade-card" style="animation-delay:0.5s">
      <h3>🗓 Development Timeline</h3>
      <p>Q2 2024 – Complete Leadership Training</p>
    </div>
  </div>

  <!-- Career Pathways -->
  <div id="pathways" class="tab-content">
    <h2>Career Pathways</h2>
    <select>
      <option>All Departments</option>
      <option>HR</option>
      <option>Finance</option>
      <option>IT</option>
      <option>Marketing</option>
    </select>
  </div>

  <!-- Readiness Assessment -->
  <div id="readiness" class="tab-content">
    <h2>Readiness Assessment</h2>
    <p><b>Overall Score:</b> 78</p>
  </div>

</div>

<footer>
  © 2025 Employee Career Development Portal
</footer>

<script>
  const buttons = document.querySelectorAll('.tab-btn');
  const contents = document.querySelectorAll('.tab-content');
  const sidebar = document.getElementById("sidebar");
  const mainContent = document.getElementById("main-content");
  const navTabs = document.getElementById("nav-tabs");

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      buttons.forEach(b => b.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));

      btn.classList.add('active');
      const activeTab = document.getElementById(btn.dataset.tab);
      activeTab.classList.add('active');

      // ✅ Reset animation for Development Plan
      if (btn.dataset.tab === "development") {
        const cards = activeTab.querySelectorAll(".fade-card");
        cards.forEach(card => {
          card.style.animation = "none";
          card.offsetHeight; // trigger reflow
          card.style.animation = "";
        });
      }
    });
  });

  document.getElementById("toggle-sidebar").onclick = function() {
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("collapsed");
    navTabs.classList.toggle("collapsed");
  };
</script>

</body>
</html>
