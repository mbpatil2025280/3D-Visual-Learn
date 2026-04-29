<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_email'])) {
  header("Location: index.html");
  exit();
}

$user_email = $_SESSION['user_email'];

// Generate avatar initials from email
$initials = strtoupper(substr($user_email, 0, 2));

// Avatar color based on email hash
$colors = ['#00f2fe','#4facfe','#a855f7','#f472b6','#34d399','#fb923c','#60a5fa'];
$color  = $colors[abs(crc32($user_email)) % count($colors)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | 3D Visual Learning</title>
  <link rel="stylesheet" href="style.css">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#00f2fe">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <style>
    body { display: block; text-align: left; font-family: 'Outfit', sans-serif; }

    /* ── Profile avatar in top-right header ── */
    .main-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 14px;
    }

    .header-user {
      display: flex;
      align-items: center;
      gap: 12px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.09);
      border-radius: 50px;
      padding: 8px 18px 8px 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .header-user:hover {
      background: rgba(0,242,254,0.1);
      border-color: rgba(0,242,254,0.35);
      transform: translateY(-1px);
      box-shadow: 0 6px 24px rgba(0,242,254,0.12);
    }

    .avatar-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: <?= $color ?>;
      color: #fff;
      font-weight: 700;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 0 2px rgba(255,255,255,0.15), 0 0 14px <?= $color ?>55;
      flex-shrink: 0;
      letter-spacing: 0.5px;
    }

    .header-email {
      font-size: 0.88rem;
      color: #cbd5e1;
      font-weight: 500;
      max-width: 200px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* ── Sidebar profile section ── */
    .sidebar-profile {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 16px;
      margin: 0 0 12px 0;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.09);
      border-radius: 14px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.25s ease;
    }

    .sidebar-profile:hover {
      background: rgba(0,242,254,0.08);
      border-color: rgba(0,242,254,0.25);
    }

    .sidebar-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: <?= $color ?>;
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 0 12px <?= $color ?>66;
    }

    .sidebar-user-info {
      min-width: 0;
    }

    .sidebar-user-name {
      font-size: 0.82rem;
      font-weight: 700;
      color: #e2e8f0;
      letter-spacing: 0.3px;
    }

    .sidebar-user-email {
      font-size: 0.73rem;
      color: #64748b;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      max-width: 130px;
    }

    .online-dot {
      width: 8px;
      height: 8px;
      background: #4ade80;
      border-radius: 50%;
      margin-left: auto;
      flex-shrink: 0;
      box-shadow: 0 0 6px #4ade8099;
      animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {
      0%,100% { transform: scale(1); opacity: 1; }
      50%      { transform: scale(1.4); opacity: 0.6; }
    }
  </style>
</head>
<body>

  <div class="admin-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">🎓 3D Visual Learn</div>

      <!-- Sidebar Profile Card -->
      <a href="profile.php" class="sidebar-profile">
        <div class="sidebar-avatar"><?= $initials ?></div>
        <div class="sidebar-user-info">
          <div class="sidebar-user-name">My Account</div>
          <div class="sidebar-user-email"><?= htmlspecialchars($user_email) ?></div>
        </div>
        <div class="online-dot"></div>
      </a>

      <a href="dashboard.php" class="active"><span>📊</span> Dashboard</a>
      <a href="#"><span>📚</span> My Courses</a>
      <a href="#"><span>💳</span> Transactions</a>
      <a href="settings.php"><span>⚙️</span> Settings</a>
      <a href="profile.php"><span>👤</span> Profile</a>
      <a href="logout.php" style="margin-top: auto; color: #ff4757;"><span>🚪</span> Log Out</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

      <div class="main-header">
        <div>
          <h1>Welcome back! 👋</h1>
          <p style="color:#94a3b8;font-size:1.05rem;margin-top:5px;">
            Select a curriculum standard below to subscribe to our interactive 3D learning modules.
          </p>
        </div>

        <!-- Header User Pill -->
        <a href="profile.php" class="header-user">
          <div class="avatar-circle"><?= $initials ?></div>
          <span class="header-email"><?= htmlspecialchars($user_email) ?></span>
        </a>
      </div>

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="welcome-banner-inner">
          <div class="welcome-text-block">
            <h2 class="welcome-title">🌐 Welcome to 3D Visual Learn!</h2>
            <p class="welcome-desc">This interactive platform helps students understand concepts through immersive 3D animations and visual storytelling. Instead of just reading theory, learners can see real-world simulations, making complex topics <strong>easier and faster to grasp.</strong></p>
          </div>
          <div class="welcome-features">
            <div class="feature-item"><span class="feature-icon">🎮</span><div><div class="feature-title">3D Animated Lessons</div><div class="feature-desc">Immersive 3D visuals for every topic</div></div></div>
            <div class="feature-item"><span class="feature-icon">🕹️</span><div><div class="feature-title">Interactive Controls</div><div class="feature-desc">Engage and explore at your own pace</div></div></div>
            <div class="feature-item"><span class="feature-icon">💡</span><div><div class="feature-title">Visual Explanations</div><div class="feature-desc">Concepts explained through simulation</div></div></div>
            <div class="feature-item"><span class="feature-icon">🚀</span><div><div class="feature-title">Engaging Experience</div><div class="feature-desc">Learn smarter, not harder</div></div></div>
          </div>
        </div>
        <p class="welcome-footer">Explore, interact, and learn smarter with <strong style="color:var(--accent-primary);">3D Visual Learn!</strong> 🌟</p>
      </div>

      <!-- Pricing Grid -->
      <div class="grid" style="padding:0;max-width:100%;">

        <a href="purchase.php?course=5th_standard">
          <div class="icon-wrapper">📘</div>
          <div class="card-title">5th Standard</div>
          <div class="card-subtitle">Foundational Science</div>
          <div class="price-tag">₹ 299 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=6th_standard">
          <div class="icon-wrapper">📗</div>
          <div class="card-title">6th Standard</div>
          <div class="card-subtitle">General Science</div>
          <div class="price-tag">₹ 399 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=7th_standard">
          <div class="icon-wrapper">📙</div>
          <div class="card-title">7th Standard</div>
          <div class="card-subtitle">Advanced Basics</div>
          <div class="price-tag">₹ 499 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=8th_standard">
          <div class="icon-wrapper">📕</div>
          <div class="card-title">8th Standard</div>
          <div class="card-subtitle">Fundamental Logic</div>
          <div class="price-tag">₹ 599 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=9th_standard">
          <div class="icon-wrapper">📘</div>
          <div class="card-title">9th Standard</div>
          <div class="card-subtitle">Pre-Secondary School</div>
          <div class="price-tag">₹ 799 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=10th_standard">
          <div class="icon-wrapper">📗</div>
          <div class="card-title">10th Standard</div>
          <div class="card-subtitle">Secondary Board</div>
          <div class="price-tag">₹ 999 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=11th_standard">
          <div class="icon-wrapper">📙</div>
          <div class="card-title">11th Standard</div>
          <div class="card-subtitle">Higher Secondary I</div>
          <div class="price-tag">₹ 1,199 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

        <a href="purchase.php?course=12th_standard">
          <div class="icon-wrapper">📕</div>
          <div class="card-title">12th Standard</div>
          <div class="card-subtitle">Higher Secondary II</div>
          <div class="price-tag">₹ 1,499 <span class="price-sub">/ year</span></div>
          <div class="subscribe-btn">Subscribe</div>
        </a>

      </div>
    </main>
  </div>

  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('service-worker.js'));
    }
  </script>
</body>
</html>
