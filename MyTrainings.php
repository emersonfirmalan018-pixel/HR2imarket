<?php
session_start();
include 'db.php';

// Handle Add / Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_training'])) {
    $id          = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title       = trim($_POST['title']);
    $date        = $_POST['date'];
    $time        = $_POST['time'];
    $duration    = floatval($_POST['duration']);
    $location    = trim($_POST['location']);
    $description = trim($_POST['description']);

    if ($id > 0) {
        // ✅ Update
        $stmt = $conn->prepare("UPDATE trainings 
            SET title=?, training_date=?, training_time=?, duration=?, location=?, description=? 
            WHERE id=?");
        $stmt->bind_param("sss dssi", $title, $date, $time, $duration, $location, $description, $id);
    } else {
        // ✅ Insert
        $stmt = $conn->prepare("INSERT INTO trainings 
            (title, training_date, training_time, duration, location, description) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdss", $title, $date, $time, $duration, $location, $description);
    }

    if (!$stmt->execute()) {
        die("❌ SQL Error: " . $stmt->error);
    }
    $stmt->close();
    header("Location: MyTrainings.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM trainings WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: MyTrainings.php");
    exit();
}

// Fetch Trainings
$trainings = $conn->query("SELECT * FROM trainings ORDER BY training_date ASC, training_time ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Training Management - My Trainings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; }
    .container { width: 95%; margin: 20px auto; }
    .header { background: #0099e6; padding: 15px; color: white; text-align: center; border-radius: 10px; }
    .tabs { display: flex; margin-top: 15px; border-bottom: 1px solid #ccc; }
    .tabs a { padding: 10px 20px; text-decoration: none; color: #333; }
    .tabs a.active { border-bottom: 3px solid #0099e6; font-weight: bold; }

    .flex { display: flex; gap: 20px; margin-top: 20px; }
    .form-card, .table-card { background: white; padding: 20px; border-radius: 10px; flex: 1; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }

    .form-card input, .form-card select, .form-card textarea { width: 100%; padding: 8px; margin: 6px 0; border: 1px solid #ccc; border-radius: 5px; }
    .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; }
    .btn-primary { background: #0099e6; color: white; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-sm { font-size: 12px; padding: 5px 10px; }

/* ===== Table Card ===== */
.table-card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  flex: 1;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
  overflow-x: auto;   /* ✅ Enables horizontal scroll */
}

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  min-width: 600px;   /* ✅ Prevents squashing too much */
}

thead {
  background: #0099e6;
  color: #fff;
  text-align: left;
}

th, td {
  padding: 12px 15px;
  font-size: 14px;
  vertical-align: middle;
  white-space: nowrap; /* ✅ Keeps text in one line */
}

/* Mobile Fix */
@media (max-width: 600px) {
  .table-card {
    padding: 10px;
  }
  table {
    font-size: 13px;
    min-width: 500px; /* ✅ Smaller minimum width */
  }
  th, td {
    padding: 8px 10px;
  }
}


/* ===== Buttons inside table ===== */
.actions {
  display: flex;
  gap: 5px;
}

.btn-sm {
  font-size: 12px;
  padding: 6px 12px;
  border-radius: 20px;
}

/* Delete button */
.btn-danger {
  background: #dc3545;
  color: white;
  border: none;
  transition: background 0.2s;
}
.btn-danger:hover {
  background: #c82333;
}

/* Edit button */
.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  transition: background 0.2s;
}
.btn-secondary:hover {
  background: #565e64;
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
  transition: width 0.3s ease, transform 0.3s ease;
  overflow: hidden;
  z-index: 1000;
}

.sidebar.collapsed {
  width: 70px;
}

.sidebar h2 {
  font-size: 22px;
  margin-bottom: 25px;
  transition: opacity 0.3s;
  white-space: nowrap;
}

.sidebar.collapsed h2 {
  opacity: 0;
  pointer-events: none;
}

.sidebar ul {
  list-style: none;
  width: 100%;
  padding: 0;
  margin: 0;
}

.sidebar ul li {
  width: 100%;
}

.sidebar ul li a {
  display: flex;
  align-items: center;
  padding: 14px 20px;
  color: #fff;
  text-decoration: none;
  font-size: 15px;
  transition: background 0.3s, padding 0.3s;
}

.sidebar ul li a i {
  margin-right: 12px;
  min-width: 20px;
  text-align: center;
  font-size: 18px;
}

.sidebar.collapsed ul li a span {
  display: none;
}

.sidebar ul li a:hover {
  background: #3d4f8b;
  padding-left: 25px;
}

/* Toggle button */
.toggle-btn {
  position: absolute;
  top: 20px;
  right: -15px;
  background: #2b3a67;
  border-radius: 50%;
  padding: 8px;
  cursor: pointer;
  color: #fff;
  border: 2px solid #fff;
  transition: background 0.3s;
  font-size: 14px;
}
.toggle-btn:hover {
  background: #3d4f8b;
}

/* ===== Main Content (Fixed) ===== */
.main-content {
  margin-left: 250px;         /* space for sidebar */
  padding: 25px;
  transition: margin-left 0.3s ease;
  min-height: 100vh;
  background: #f4f6f9;

  display: flex;
  flex-direction: column;
  gap: 20px;                  /* spacing between header/tabs/content */
}

/* When sidebar is collapsed */
.sidebar.collapsed ~ .main-content {
  margin-left: 70px;
}

/* Inner container (centering + max width) */
.main-content .container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

/* Responsive Fix */
@media (max-width: 992px) {
  .main-content {
    margin-left: 70px;
    padding: 20px;
  }
}

@media (max-width: 600px) {
  .main-content {
    margin-left: 0;
    padding: 15px;
  }
}


/* ===== Responsive ===== */
@media (max-width: 992px) {
  .sidebar {
    width: 70px; /* Auto-collapse on tablets */
  }
  .sidebar h2,
  .sidebar ul li a span {
    display: none;
  }
  .main-content {
    margin-left: 70px;
  }
}

@media (max-width: 600px) {
  .sidebar {
    transform: translateX(-100%);
    width: 220px; /* Slide-in drawer style */
  }
  .sidebar.active {
    transform: translateX(0);
  }
  .main-content {
    margin-left: 0;
    padding: 15px;
  }
  .toggle-btn {
    right: -45px; /* Push button outside for mobile */
    top: 15px;
  }
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
        </ul>
    </div>
<div class="main-content container">
  <div class="header"><h2>Training Management</h2></div>

  <div class="tabs">
    <a href="MyTrainings.php" class="active">Training Events</a>
    <a href="#">Employee Assignment</a>
    <a href="#">Attendance & Completion</a>
    <a href="#">Backup</a>
  </div>

  <div class="flex">
    <!-- Training Form -->
    <div class="form-card">
      <h3>Training Event Setup</h3>
      <form method="post">
        <input type="hidden" name="id" id="training_id">

        <label>Title</label>
        <input type="text" name="title" id="title" required>

        <label>Date</label>
        <input type="date" name="date" id="date" required>

        <label>Time</label>
        <input type="time" name="time" id="time" required>

        <label>Duration (hours)</label>
        <input type="number" step="0.5" name="duration" id="duration" required>

        <label>Location</label>
        <input type="text" name="location" id="location">

        <label>Description</label>
        <textarea name="description" id="description"></textarea>

        <button type="submit" name="save_training" class="btn btn-primary">Save Training</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
      </form>
    </div>

    <!-- Training Table -->
    <div class="table-card">
      <h3>Scheduled Trainings</h3>
      <input type="text" class="search-box" id="search" placeholder="Search trainings...">
      <table id="trainingsTable">
        <thead>
          <tr>
            <th>Title</th>
            <th>Date</th>
            <th>Time</th>
            <th>Duration</th>
            <th>Location</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $trainings->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td><?= htmlspecialchars($row['training_date']); ?></td>
            <td><?= htmlspecialchars(substr($row['training_time'], 0, 5)); ?></td>
            <td><?= htmlspecialchars($row['duration']); ?></td>
            <td><?= htmlspecialchars($row['location']); ?></td>
            <td class="actions">
              <button class="btn btn-secondary btn-sm" onclick="editTraining(<?= $row['id']; ?>,'<?= htmlspecialchars($row['title']); ?>','<?= $row['training_date']; ?>','<?= $row['training_time']; ?>','<?= $row['duration']; ?>','<?= htmlspecialchars($row['location']); ?>','<?= htmlspecialchars($row['description']); ?>')">Edit</button>
              <a href="MyTrainings.php?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this training?');">Delete</a>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function editTraining(id, title, date, time, duration, location, description) {
  document.getElementById("training_id").value = id;
  document.getElementById("title").value = title;
  document.getElementById("date").value = date;
  document.getElementById("time").value = time;
  document.getElementById("duration").value = duration;
  document.getElementById("location").value = location;
  document.getElementById("description").value = description;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Simple Search
document.getElementById("search").addEventListener("keyup", function() {
  var value = this.value.toLowerCase();
  var rows = document.querySelectorAll("#trainingsTable tbody tr");
  rows.forEach(function(row) {
    row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
  });
});
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  if (window.innerWidth <= 600) {
    sidebar.classList.toggle("active");
  } else {
    sidebar.classList.toggle("collapsed");
  }
}

</script>
</script>
</body>
</html>
