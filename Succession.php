<?php
session_start();
include 'db.php';

// ==== Handle Key Role Insert ====
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_key_role'])) {
    $role_name = $_POST['role_name'];
    $criticality = $_POST['criticality'];
    $timeline = $_POST['timeline'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO succession_key_roles (role_name, criticality, timeline, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $role_name, $criticality, $timeline, $notes);
    $stmt->execute();
    $stmt->close();

    header("Location: Succession.php?tab=roles");
    exit();
}

// ==== Handle Delete Key Role ====
if (isset($_GET['delete_role'])) {
    $id = intval($_GET['delete_role']);
    $conn->query("DELETE FROM succession_key_roles WHERE id=$id");
    header("Location: Succession.php?tab=roles");
    exit();
}

// ==== Handle Successor Insert ====
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_successor'])) {
    $key_role_id = $_POST['key_role_id'];
    $user_id = $_POST['user_id'];
    $readiness = $_POST['readiness'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO succession_successors (key_role_id, user_id, readiness, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $key_role_id, $user_id, $readiness, $notes);
    $stmt->execute();
    $stmt->close();

    header("Location: Succession.php?tab=successors");
    exit();
}

// ==== Handle Delete Successor ====
if (isset($_GET['delete_successor'])) {
    $id = intval($_GET['delete_successor']);
    $conn->query("DELETE FROM succession_successors WHERE id=$id");
    header("Location: Succession.php?tab=successors");
    exit();
}

// ==== Fetch Data ====
$key_roles = $conn->query("SELECT * FROM succession_key_roles ORDER BY created_at DESC");
$users = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
$successors = $conn->query("SELECT ss.id, kr.role_name, u.username, ss.readiness, ss.notes 
    FROM succession_successors ss
    JOIN succession_key_roles kr ON ss.key_role_id = kr.id
    JOIN users u ON ss.user_id = u.id
    ORDER BY ss.created_at DESC");

// Active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'roles';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Succession Planning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; }
    .container { width: 95%; margin: 20px auto; }
    .header { background: #fff; padding: 20px; font-size: 22px; font-weight: bold; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

    .tabs { display: flex; margin-top: 20px; border-bottom: 2px solid #ddd; }
    .tabs a { padding: 12px 20px; text-decoration: none; color: #333; font-weight: 500; }
    .tabs a.active { border-bottom: 3px solid #2b3a67; color: #2b3a67; font-weight: bold; }

    .flex { display: flex; gap: 20px; margin-top: 20px; }
    .form-card, .table-card { background: white; padding: 20px; border-radius: 10px; flex: 1; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }

    .form-card input, .form-card select, .form-card textarea { width: 100%; padding: 10px; margin: 6px 0; border: 1px solid #ccc; border-radius: 6px; }
    .btn { padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-primary { background: #2b3a67; color: white; }
    .btn-danger { background: #dc3545; color: white; font-size: 14px; padding: 6px 12px; border-radius: 4px; }

    table { width: 100%; border-collapse: collapse; }
    table th, table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    table th { background: #f5f5f5; }
    h3 { margin-top: 0; }
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
          z-index: 1000;
        }

        .sidebar.collapsed { width: 70px; }
        .sidebar h2 { font-size: 20px; margin-bottom: 20px; transition: opacity 0.3s; white-space: nowrap; }
        .sidebar.collapsed h2 { opacity: 0; pointer-events: none; }

        .sidebar ul { list-style: none; width: 100%; padding: 0; }
        .sidebar ul li a { display: flex; align-items: center; padding: 12px 20px; color: #fff; text-decoration: none; transition: 0.3s; }
        .sidebar ul li a i { margin-right: 12px; min-width: 20px; text-align: center; font-size: 18px; }
        .sidebar.collapsed ul li a span { display: none; }
        .sidebar ul li a:hover { background: #3d4f8b; }

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
          transition: 0.3s;
          font-size: 14px;
        }
        .toggle-btn:hover { background: #3d4f8b; }

        .main-content {
          margin-left: 250px;
          padding: 20px;
          transition: margin-left 0.3s;
        }
        .sidebar.collapsed ~ .main-content { margin-left: 70px; }
  </style>
</head>
<body>
      <div class="sidebar" id="sidebar">
    <h2>IMARKET</h2>
    <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
    <ul>
      <li><a href="admin_dashboard.php"><i class="fas fa-home"></i><span> My Dashboard</span></a></li>
      <li><a href="MyProfile.php"><i class="fas fa-user-check"></i><span> Competency Profile</span></a></li>
      <li><a href="MyLearning.php"><i class="fas fa-book"></i><span> My Learning</span></a></li>
      <li><a href="MyTrainings.php"><i class="fas fa-chalkboard-teacher"></i><span> My Trainings</span></a></li>
      <li><a href="Succession.php"><i class="fas fa-briefcase"></i><span> Succession</span></a></li>
      <li><a href="ESS.php"><i class="fas fa-id-badge"></i><span> Employee Self-Service</span></a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
    </ul>
  </div>
  <div class="main-content" id="main">
    <div class="header">Succession Planning</div>

    <!-- Tabs -->
    <div class="tabs">
      <a href="Succession.php?tab=roles" class="<?php echo $tab=='roles'?'active':''; ?>">Key Roles</a>
      <a href="Succession.php?tab=successors" class="<?php echo $tab=='successors'?'active':''; ?>">Successors</a>
      <a href="Succession.php?tab=tracking" class="<?php echo $tab=='tracking'?'active':''; ?>">Readiness Tracking</a>
    </div>

    <?php if ($tab == 'roles') { ?>
      <h3>Key Role Identification</h3>
      <div class="flex">
        <!-- Form -->
        <div class="form-card">
          <form method="post">
            <label>Select Role</label>
            <input type="text" name="role_name" placeholder="Enter role" required>

            <label>Criticality Level</label>
            <select name="criticality">
              <option>High</option>
              <option>Medium</option>
              <option>Low</option>
            </select>

            <label>Succession Timeline</label>
            <select name="timeline">
              <option>Immediate (0-6 months)</option>
              <option>Short-term (6-12 months)</option>
              <option>Long-term (1-3 years)</option>
            </select>

            <label>Notes</label>
            <textarea name="notes" placeholder="Role-specific succession notes"></textarea>

            <button type="submit" name="add_key_role" class="btn btn-primary">Add Key Role</button>
          </form>
        </div>

        <!-- Table -->
        <div class="table-card">
          <table>
            <thead>
              <tr>
                <th>ROLE</th>
                <th>CRITICALITY</th>
                <th>TIMELINE</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $key_roles->fetch_assoc()) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                <td><?php echo htmlspecialchars($row['criticality']); ?></td>
                <td><?php echo htmlspecialchars($row['timeline']); ?></td>
                <td>
                  <a href="Succession.php?delete_role=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this role?');">Delete</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>

    <?php if ($tab == 'successors') { ?>
      <h3>Assign Successors</h3>
      <div class="flex">
        <!-- Form -->
        <div class="form-card">
          <form method="post">
            <label>Select Key Role</label>
            <select name="key_role_id" required>
              <option value="">-- Select Role --</option>
              <?php 
              $roles_for_dropdown = $conn->query("SELECT id, role_name FROM succession_key_roles");
              while ($r = $roles_for_dropdown->fetch_assoc()) {
                  echo "<option value='".$r['id']."'>".$r['role_name']."</option>";
              }
              ?>
            </select>

            <label>Select Employee</label>
            <select name="user_id" required>
              <option value="">-- Select Employee --</option>
              <?php 
              $users_for_dropdown = $conn->query("SELECT id, username FROM users");
              while ($u = $users_for_dropdown->fetch_assoc()) {
                  echo "<option value='".$u['id']."'>".$u['username']."</option>";
              }
              ?>
            </select>

            <label>Readiness</label>
            <select name="readiness">
              <option>Ready Now</option>
              <option>1-2 Years</option>
              <option>3+ Years</option>
            </select>

            <label>Notes</label>
            <textarea name="notes" placeholder="Successor notes"></textarea>

            <button type="submit" name="add_successor" class="btn btn-primary">Add Successor</button>
          </form>
        </div>

        <!-- Table -->
        <div class="table-card">
          <table>
            <thead>
              <tr>
                <th>ROLE</th>
                <th>EMPLOYEE</th>
                <th>READINESS</th>
                <th>NOTES</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $successors->fetch_assoc()) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['readiness']); ?></td>
                <td><?php echo htmlspecialchars($row['notes']); ?></td>
                <td>
                  <a href="Succession.php?delete_successor=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this successor?');">Delete</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>

    <?php if ($tab == 'tracking') { ?>
      <h3>Readiness Tracking</h3>
      <p>📊 Here we will later add charts and tracking of successor readiness.</p>
    <?php } ?>
  </div>
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
      document.getElementById("main").classList.toggle("collapsed");
    }
</body>
</html>
