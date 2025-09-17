<?php session_start(); ?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - iMarket HR</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #dce6f0;
      color: #333;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 50px;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .brand img {
      height: 40px;
    }
    .brand span {
      font-size: 20px;
      font-weight: 700;
      color: #2b3a67;
    }

    /* Nav Container */
    .nav-center {
      background: #f8f9fb;
      padding: 8px 20px;
      border-radius: 8px;
      display: flex;
      gap: 25px;
    }
    .nav-center a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      transition: color 0.3s;
    }
    .nav-center a:hover {
      color: #2563eb;
    }

    /* Dropdown */
    .dropdown {
      position: relative;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      top: 110%;
      right: 0;
      background: #fff;
      border-radius: 6px;
      min-width: 180px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      z-index: 10;
    }
    .dropdown-content li {
      list-style: none;
    }
    .dropdown-content li a {
      display: block;
      padding: 12px 15px;
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.2s;
    }
    .dropdown-content li a:hover {
      background: #f1f5f9;
      color: #2563eb;
    }
    .dropdown:hover .dropdown-content {
      display: block;
    }

    /* About Section */
    .about-section {
      text-align: center;
      padding: 80px 50px;
      background: #cdd9e7;
    }
    .about-section h1 {
      font-size: 40px;
      font-weight: 800;
      margin-bottom: 20px;
    }
    .about-section p {
      max-width: 850px;
      margin: 0 auto 20px;
      line-height: 1.6;
      font-size: 18px;
    }

    /* Stats */
    .stats {
      display: flex;
      justify-content: center;
      gap: 60px;
      margin: 50px 0;
      flex-wrap: wrap;
    }
    .stat {
      text-align: center;
    }
    .stat h2 {
      font-size: 32px;
      font-weight: bold;
      color: #5a4dff;
    }
    .stat p {
      margin-top: 8px;
      font-size: 14px;
      text-transform: uppercase;
      color: #555;
    }

    /* Contact Info */
    .contact-info {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin-top: 30px;
    }
    .contact-box {
      background: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .contact-box i {
      color: #5a4dff;
      font-size: 18px;
    }
  </style>
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar">
    <!-- Logo + Text -->
    <div class="brand">
      <img src="logo.png" alt="iMarket Logo">
      <span>iMarket</span>
    </div>

    <!-- Center Links in Container -->
    <div class="nav-center">
      <a href="index.html">Home</a>
      <a href="about.php">About Us</a>
    </div>

    <!-- Sign In Dropdown -->
    <div class="dropdown">
      <a href="#" class="btn">Sign In ▾</a>
      <ul class="dropdown-content">
        <li><a href="login.php?role=employee">Employee Access</a></li>
        <li><a href="login.php?role=admin">Admin Panel</a></li>
      </ul>
    </div>
  </nav>

  <section class="about-section">
    <h1>About Us</h1>
    <p>
      At iMarket HR, we believe people are the foundation of success. As a trusted HR platform, we support
      employees and organizations by managing skills, learning, training, succession planning, and self-service
      —all in one system.
    </p>
    <p>
      Our mission is to create a seamless HR experience powered by technology, efficiency, and care. With
      tools for growth, career development, and everyday HR needs, iMarket HR is more than just a system—
      it’s your partner in building a smarter, stronger workforce.
    </p>

    <div class="stats">
      <div class="stat">
        <h2 data-target="50">0+</h2>
        <p>Skills</p>
      </div>
      <div class="stat">
        <h2 data-target="220">0+</h2>
        <p>Employees Managed</p>
      </div>
      <div class="stat">
        <h2 data-target="99.9">0%</h2>
        <p>Development</p>
      </div>
    </div>

    <div class="contact-info">
      <div class="contact-box">
        <i class="fa fa-globe"></i> www.imarket.com
      </div>
      <div class="contact-box">
        <i class="fa fa-phone"></i> +1 (555) 123-4567
      </div>
      <div class="contact-box">
        <i class="fa fa-envelope"></i> imarket.hr@gmail.com
      </div>
    </div>
  </section>

  <script>
    // Count-up animation
    function animateCount(el) {
      const target = parseFloat(el.getAttribute("data-target"));
      const isPercent = el.innerText.includes("%");
      const isPlus = el.innerText.includes("+");

      let count = 0;
      const speed = target / 100;

      const updateCount = () => {
        if (count < target) {
          count += speed;
          el.innerText = (isPercent ? count.toFixed(1) : Math.floor(count)) + (isPercent ? "%" : (isPlus ? "+" : ""));
          requestAnimationFrame(updateCount);
        } else {
          el.innerText = target + (isPercent ? "%" : (isPlus ? "+" : ""));
        }
      };
      updateCount();
    }

    const statsSection = document.querySelector('.stats');
    const statNumbers = document.querySelectorAll('.stat h2');
    let started = false;

    window.addEventListener('scroll', () => {
      const sectionPos = statsSection.getBoundingClientRect().top;
      const screenPos = window.innerHeight;

      if (sectionPos < screenPos && !started) {
        statNumbers.forEach(num => animateCount(num));
        started = true;
      }
    });
  </script>
</body>
</html>
