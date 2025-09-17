<?php 
session_start();
include 'db.php';

// Check if logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php?role=admin");
    exit();
}

// Fetch employees (not admins)
$employees = $conn->query("SELECT * FROM users WHERE role='employee'");

// Fetch departments
$departments = $conn->query("SELECT * FROM departments");

// Fetch managers (any employee or admin can be a manager)
$managers = $conn->query("SELECT id, username FROM users");

// Update employee info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $id = intval($_POST['employee']);
    $role = $_POST['role'];
    $department_id = !empty($_POST['department_id']) ? intval($_POST['department_id']) : null;
    $manager_id = !empty($_POST['manager_id']) ? intval($_POST['manager_id']) : null;

    $stmt = $conn->prepare("UPDATE users SET role=?, department_id=?, manager_id=? WHERE id=?");
    $stmt->bind_param("siii", $role, $department_id, $manager_id, $id);
    $stmt->execute();

    header("Location: ESS.php");
    exit();
}

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='employee'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ESS.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Employee Self-Service</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { 
      font-family: Arial, sans-serif; 
      margin: 0; 
      background: #f4f6f9; 
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
    }
    .sidebar.collapsed {
      width: 70px; 
    }
    .sidebar h2 { 
      font-size: 20px; 
      margin-bottom: 20px; 
      transition: opacity 0.3s; 
    }
    .sidebar.collapsed h2 { 
      opacity: 0; 
      pointer-events: none; 
    }
    .sidebar ul { 
      list-style: none; 
      width: 100%; 
      padding: 0; 
    }
    .sidebar ul li a {
      display: flex; 
      align-items: center; 
      padding: 12px 20px;
      color: #fff; 
      text-decoration: none; 
      transition: 0.3s;
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
      position: absolute; top: 20px; right: -20px;
      background: #2b3a67; border-radius: 50%;
      padding: 10px; cursor: pointer; color: #fff;
      border: 2px solid #fff; transition: 0.3s;
    }
    .toggle-btn:hover { background: #3d4f8b; }
    .main-content { 
      margin-left: 250px; 
      padding: 20px; 
      width: calc(100% - 250px); 
    }
    h2 { 
      margin-bottom: 20px; 
    }
    .container { 
      display: flex; 
      gap: 20px; 
    }
    .form-box, .table-box {
      background: #fff; 
      padding: 20px; 
      border-radius: 10px; 
      flex: 1; 
    }
    label { 
      display: block; 
      margin-bottom: 5px; 
      font-weight: bold; 
    }
    select, input { 
      width: 100%; 
      padding: 10px; 
      margin-bottom: 15px; 
      border: 1px solid #ccc; 
      border-radius: 5px; 
    }
    button { 
      padding: 10px 20px; 
      border: none; 
      border-radius: 5px; 
      cursor: pointer; 
    }
    .update { 
      background: #38a169; 
      color: white; 
    }
    .reset { 
      background: #ccc; 
    }
    table { 
      width: 100%; 
      border-collapse: collapse; 
      margin-top: 10px; 
    }
    th, td { 
      border: 1px solid #ddd; 
      padding: 12px; 
      text-align: center; 
    }
    th { 
      background: #5a38d3; 
      color: white; 
    }
    tr:nth-child(even) { 
      background: #f9f9f9;
     }
    .actions a { 
      padding: 6px 12px; 
      margin: 2px; 
      border-radius: 5px; 
      text-decoration: none; 
      font-size: 14px; 
    }
    .edit { 
      background: #38a169; 
      color: white; 
    }
    .delete { 
      background: #e53e3e; 
      color: white; 
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

  <!-- Main Content -->
  <div class="main-content">
    <h2>👔 Employee Info Update</h2>
    <div class="container">
      <!-- Update Form -->
      <div class="form-box">
        <form method="POST">
          <label>Employee</label>
          <select name="employee" required>
            <?php while($emp = $employees->fetch_assoc()): ?>
              <option value="<?= $emp['id']; ?>"><?= htmlspecialchars($emp['username']); ?></option>
            <?php endwhile; ?>
          </select>

          <label>Role</label>
          <select name="role" required>
            <option value="employee">Employee</option>
            <option value="admin">Admin</option>
          </select>

          <label>Department</label>
          <select name="department_id">
            <option value="">-- Select Department --</option>
            <?php while($dept = $departments->fetch_assoc()): ?>
              <option value="<?= $dept['id']; ?>"><?= htmlspecialchars($dept['name']); ?></option>
            <?php endwhile; ?>
          </select>

          <label>Manager</label>
          <select name="manager_id">
            <option value="">-- Select Manager --</option>
            <?php while($mgr = $managers->fetch_assoc()): ?>
              <option value="<?= $mgr['id']; ?>"><?= htmlspecialchars($mgr['username']); ?></option>
            <?php endwhile; ?>
          </select>

          <button type="submit" name="update_info" class="update">Update Info</button>
          <button type="reset" class="reset">Reset</button>
        </form>
      </div>

      <!-- Employee Table -->
      <div class="table-box">
        <table>
          <tr>
            <th>Employee</th>
            <th>Role</th>
            <th>Department</th>
            <th>Manager</th>
            <th>Actions</th>
          </tr>
          <?php 
          $result = $conn->query("
            SELECT u.id, u.username, u.role, d.name AS dept_name, m.username AS manager_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN users m ON u.manager_id = m.id
            WHERE u.role='employee'
          ");
          while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['role']); ?></td>
            <td><?= htmlspecialchars($row['dept_name'] ?? ''); ?></td>
            <td><?= htmlspecialchars($row['manager_name'] ?? ''); ?></td>
            <td class="actions">
              <a href="edit_user.php?id=<?= $row['id']; ?>" class="edit">Edit</a>
              <a href="ESS.php?delete=<?= $row['id']; ?>" class="delete" onclick="return confirm('Delete this employee?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>
  </div>
    <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
      document.getElementById("main").classList.toggle("collapsed");
    }
  </script>
</body>
</html>
