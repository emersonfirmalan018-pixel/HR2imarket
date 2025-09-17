<?php 
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: login.php?role=$role");
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Signup</title>
  <style>
    /* Background image with overlay and animation */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: url('signup.webp') no-repeat center center/cover;
      position: relative;
      overflow: hidden;
      animation: fadeInBg 2s ease-in-out;
    }

    /* Animated overlay gradient */
    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(120deg, rgba(0,0,0,0.4), rgba(0,0,0,0.2), rgba(0,0,0,0.4));
      animation: moveGradient 8s infinite linear;
      z-index: 0;
    }

    @keyframes moveGradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes fadeInBg {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Form container */
    .form-container {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      width: 320px;
      text-align: center;
      animation: slideUp 1.5s ease-in-out;
    }

    @keyframes slideUp {
      from {
        transform: translateY(50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
    }

    input, select, button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    button {
      background: #4CAF50;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #45a049;
      transform: scale(1.05);
    }

    /* Back button styling */
    .back-btn {
      background: #f44336;
      margin-top: 5px;
    }

    .back-btn:hover {
      background: #d32f2f;
      transform: scale(1.05);
    }

    .error {
      color: red;
      font-size: 14px;
    }
  </style>
</head>
<body>
<div class="form-container">
  <h2>Create Account</h2>
  <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role" required>
      <option value="employee">Employee</option>
      <option value="admin">Admin</option>
    </select>
    <button type="submit">Sign Up</button>
    <button type="button" class="back-btn" onclick="history.back()">Back</button>
  </form>
</div>
</body>
</html>
