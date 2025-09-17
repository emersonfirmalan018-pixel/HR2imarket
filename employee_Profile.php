<?php 
session_start();
include 'db.php';

// Example: Assume logged-in user has id=2
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2; // demo user
}
$user_id = $_SESSION['user_id'];

// ==========================
// Handle profile update
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dept  = $_POST['department'];

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, department_id=? WHERE id=?");
    $stmt->bind_param("ssii", $name, $email, $dept, $user_id);
    $stmt->execute();

    $_SESSION['phone'] = $phone;
    $msg = "Profile updated successfully!";
}

// ==========================
// Fetch user info
// ==========================
$sql = "SELECT u.username, u.email, d.name as department, d.id as dept_id 
        FROM users u 
        LEFT JOIN departments d ON u.department_id = d.id 
        WHERE u.id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : "+1-555-0123";

// ==========================
// Handle request submission
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    $request_type = $_POST['request_type'];
    $details = $_POST['details'];

    $_SESSION['requests'][] = [
        "id" => uniqid(),
        "type" => $request_type,
        "details" => $details,
        "date" => date("m/d/Y, g:i:s A")
    ];
}

// ==========================
// Handle request delete
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_request'])) {
    $id = $_POST['id'];
    $_SESSION['requests'] = array_filter($_SESSION['requests'], function($r) use ($id) {
        return $r['id'] !== $id;
    });
}

// ==========================
// Handle request edit
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_request'])) {
    $id = $_POST['id'];
    $new_type = $_POST['request_type'];
    $new_details = $_POST['details'];

    foreach ($_SESSION['requests'] as &$r) {
        if ($r['id'] === $id) {
            $r['type'] = $new_type;
            $r['details'] = $new_details;
            $r['date'] = date("m/d/Y, g:i:s A (edited)");
            break;
        }
    }
}

$recent_requests = isset($_SESSION['requests']) ? $_SESSION['requests'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Personal Profile & Services</title>
  <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
/* ===== Global ===== */
body {
  font-family: Arial, sans-serif;
  background: #f8f9fa;
  margin: 0;
  padding: 0;
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

/* ===== Cards and Layout ===== */
.card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}
.col {
  flex: 1;
  min-width: 250px;
}
input, select, textarea {
  width: 100%;
  padding: 10px;
  margin-top: 8px;
  margin-bottom: 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  background: #2e3252;
  color: #fff;
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 5px;
}
button:hover {
  background: #1b1e36;
}
.msg { color: green; font-weight: bold; }
.requests { background: #f1f3f5; padding: 12px; border-radius: 6px; margin-top: 10px; }
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
  <h2>Personal Profile & Services</h2>

  <div class="card">
    <p><strong>Welcome to Employee Self Service</strong></p>
  </div>

  <!-- Profile Management -->
  <div class="card">
    <h3>Personal Profile Management</h3>
    <?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>
    <form method="post">
      <div class="row">
        <div class="col">
          <label>Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="col">
          <label>Department</label>
          <select name="department">
            <?php
              $depts = $conn->query("SELECT * FROM departments");
              while($d = $depts->fetch_assoc()) {
                $sel = ($d['id'] == $user['dept_id']) ? "selected" : "";
                echo "<option value='{$d['id']}' $sel>{$d['name']}</option>";
              }
            ?>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="col">
          <label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">
        </div>
      </div>
      <button type="submit" name="update_profile">Update Profile</button>
    </form>
  </div>

  <!-- Request Submission -->
  <div class="card">
    <h3>Request Submission</h3>
    <div class="row">
      <div class="col">
        <form method="post">
          <label>Request Type</label>
          <select name="request_type" required>
            <option value="">Select type</option>
            <option value="Training Enrollment">Training Enrollment</option>
            <option value="Leave Request">Leave Request</option>
            <option value="IT Support">IT Support</option>
          </select>
          <label>Details</label>
          <textarea name="details" placeholder="Describe your request..." required></textarea>
          <button type="submit" name="submit_request">Submit Request</button>
        </form>
      </div>
      <div class="col">
        <h4>My Recent Requests</h4>
        <?php if (empty($recent_requests)) {
          echo "<p>No requests yet.</p>";
        } else {
          foreach (array_reverse($recent_requests) as $r) { ?>
            <div class='requests'>
              <b><?= htmlspecialchars($r['type']) ?></b><br>
              <?= htmlspecialchars($r['details']) ?><br>
              <small><?= $r['date'] ?></small><br>
              <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button type="submit" name="delete_request">Delete</button>
              </form>
              <button type="button" onclick="editRequest('<?= $r['id'] ?>','<?= htmlspecialchars($r['type'], ENT_QUOTES) ?>','<?= htmlspecialchars($r['details'], ENT_QUOTES) ?>')">Edit</button>
            </div>
          <?php }
        } ?>
      </div>
    </div>
  </div>

  <!-- Hidden Edit Form -->
  <div class="card" id="editForm" style="display:none;">
    <h3>Edit Request</h3>
    <form method="post">
      <input type="hidden" name="id" id="edit_id">
      <label>Request Type</label>
      <select name="request_type" id="edit_type" required>
        <option value="Training Enrollment">Training Enrollment</option>
        <option value="Leave Request">Leave Request</option>
        <option value="IT Support">IT Support</option>
      </select>
      <label>Details</label>
      <textarea name="details" id="edit_details" required></textarea>
      <button type="submit" name="edit_request">Update Request</button>
      <button type="button" onclick="document.getElementById('editForm').style.display='none'">Cancel</button>
    </form>
  </div>
</div>

<script>
document.getElementById("toggle-sidebar").onclick = function() {
    document.getElementById("sidebar").classList.toggle("collapsed");
    document.getElementById("main-content").classList.toggle("collapsed");
};

function editRequest(id, type, details) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_type").value = type;
    document.getElementById("edit_details").value = details;
    document.getElementById("editForm").style.display = "block";
}
</script>
</body>
</html>
