<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_email'])) {
  header("Location: index.html");
  exit();
}

include 'db.php';

$user_email = $_SESSION['user_email'];
$user_id    = $_SESSION['user_id'];

// Fetch full user info from DB
$sql    = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user   = $result->fetch_assoc();

// Fetch user's purchased courses
$purchases_sql = "SELECT * FROM purchases WHERE user_id = $user_id ORDER BY purchase_date DESC";
$purchases_res = $conn->query($purchases_sql);

// Avatar initials & color
$initials = strtoupper(substr($user_email, 0, 2));
$colors   = ['#00f2fe','#4facfe','#a855f7','#f472b6','#34d399','#fb923c','#60a5fa'];
$color    = $colors[abs(crc32($user_email)) % count($colors)];

// Member since
$member_since = date('F j, Y', strtotime($user['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | 3D Visual Learning</title>
  <link rel="stylesheet" href="style.css">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#00f2fe">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <style>
    :root {
      --avatar-color: <?= $color ?>;
    }

    body { display: block; text-align: left; font-family: 'Outfit', sans-serif; }

    /* ── Sidebar profile card (same as dashboard) ── */
    .sidebar-profile {
      display: flex; align-items: center; gap: 10px;
      padding: 14px 16px; margin: 0 0 12px 0;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.09);
      border-radius: 14px; cursor: pointer;
      text-decoration: none; transition: all 0.25s ease;
    }
    .sidebar-profile:hover { background: rgba(0,242,254,0.08); border-color: rgba(0,242,254,0.25); }
    .sidebar-avatar {
      width: 42px; height: 42px; border-radius: 50%;
      background: var(--avatar-color); color: #fff;
      font-weight: 700; font-size: 1rem;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; box-shadow: 0 0 12px var(--avatar-color)66;
    }
    .sidebar-user-name { font-size: 0.82rem; font-weight: 700; color: #e2e8f0; }
    .sidebar-user-email { font-size: 0.73rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 130px; }
    .online-dot {
      width: 8px; height: 8px; background: #4ade80; border-radius: 50%;
      margin-left: auto; flex-shrink: 0; box-shadow: 0 0 6px #4ade8099;
      animation: pulse-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:0.6;} }

    /* ── Profile page content ── */
    .profile-page {
      max-width: 780px;
      margin: 0 auto;
      padding: 10px 0 40px;
    }

    .profile-hero {
      background: linear-gradient(135deg, rgba(0,242,254,0.08) 0%, rgba(79,172,254,0.05) 100%);
      border: 1px solid rgba(0,242,254,0.15);
      border-radius: 28px;
      padding: 48px 40px;
      display: flex;
      align-items: center;
      gap: 36px;
      margin-bottom: 28px;
      position: relative;
      overflow: hidden;
    }

    .profile-hero::before {
      content: '';
      position: absolute;
      top: -60px; right: -60px;
      width: 220px; height: 220px;
      background: radial-gradient(circle, var(--avatar-color)22, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .profile-big-avatar {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: var(--avatar-color);
      color: #fff;
      font-size: 2.8rem;
      font-weight: 900;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow:
        0 0 0 4px rgba(255,255,255,0.08),
        0 0 40px var(--avatar-color)55,
        0 8px 32px rgba(0,0,0,0.4);
      position: relative;
      letter-spacing: 1px;
      animation: avatar-breathe 3s ease-in-out infinite;
    }

    @keyframes avatar-breathe {
      0%,100% { box-shadow: 0 0 0 4px rgba(255,255,255,0.08), 0 0 30px var(--avatar-color)44, 0 8px 32px rgba(0,0,0,0.4); }
      50%      { box-shadow: 0 0 0 4px rgba(255,255,255,0.12), 0 0 55px var(--avatar-color)88, 0 8px 32px rgba(0,0,0,0.4); }
    }

    .status-badge {
      position: absolute;
      bottom: 6px; right: 6px;
      width: 22px; height: 22px;
      background: #4ade80;
      border: 3px solid #020617;
      border-radius: 50%;
      box-shadow: 0 0 8px #4ade8099;
    }

    .profile-hero-info h1 {
      font-size: 2rem;
      font-weight: 900;
      background: linear-gradient(to right, #fff, #94a3b8);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 8px;
      line-height: 1.2;
    }

    .profile-email-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(0,242,254,0.08);
      border: 1px solid rgba(0,242,254,0.25);
      border-radius: 50px;
      padding: 7px 18px;
      font-size: 0.95rem;
      color: #00f2fe;
      font-weight: 600;
      margin-bottom: 14px;
      word-break: break-all;
    }

    .profile-meta {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-top: 4px;
    }

    .profile-meta-item {
      font-size: 0.82rem;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* ── Stats cards row ── */
    .profile-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 18px;
      padding: 22px 20px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      background: rgba(0,242,254,0.06);
      border-color: rgba(0,242,254,0.2);
      transform: translateY(-3px);
    }

    .stat-icon { font-size: 1.8rem; margin-bottom: 8px; }

    .stat-value {
      font-size: 1.9rem;
      font-weight: 900;
      background: linear-gradient(to right, var(--avatar-color), #4facfe);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .stat-label { font-size: 0.78rem; color: #64748b; margin-top: 4px; font-weight: 500; }

    /* ── Info Grid ── */
    .profile-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 28px;
    }

    @media (max-width: 600px) {
      .profile-grid { grid-template-columns: 1fr; }
      .profile-hero { flex-direction: column; text-align: center; padding: 36px 24px; }
      .profile-meta { justify-content: center; }
    }

    .info-card {
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 18px;
      padding: 24px 22px;
    }

    .info-card h3 {
      font-size: 0.9rem;
      font-weight: 700;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-size: 0.88rem;
    }

    .info-row:last-child { border-bottom: none; }

    .info-label { color: #64748b; font-weight: 500; }
    .info-value { color: #e2e8f0; font-weight: 600; text-align: right; word-break: break-all; }

    /* ── Purchased Courses ── */
    .courses-card {
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 18px;
      padding: 24px 22px;
      margin-bottom: 28px;
    }

    .courses-card h3 {
      font-size: 0.9rem;
      font-weight: 700;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 16px;
    }

    .course-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .course-item:last-child { border-bottom: none; }

    .course-dot {
      width: 10px; height: 10px;
      border-radius: 50%;
      background: var(--avatar-color);
      flex-shrink: 0;
      box-shadow: 0 0 6px var(--avatar-color);
    }

    .course-name { font-size: 0.9rem; color: #e2e8f0; font-weight: 600; }
    .course-date { font-size: 0.78rem; color: #64748b; margin-left: auto; }

    .no-courses {
      text-align: center;
      color: #475569;
      font-size: 0.9rem;
      padding: 20px 0;
    }

    /* ── Logout Button ── */
    .logout-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: rgba(255,71,87,0.1);
      border: 1px solid rgba(255,71,87,0.25);
      color: #ff4757;
      border-radius: 12px;
      padding: 13px 26px;
      font-size: 0.92rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .logout-btn:hover {
      background: rgba(255,71,87,0.2);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255,71,87,0.2);
    }

    /* ── Sidebar active ── */
    .sidebar a.active-profile { background: rgba(0,242,254,0.08); border-left: 3px solid #00f2fe; }
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
        <div>
          <div class="sidebar-user-name">My Account</div>
          <div class="sidebar-user-email"><?= htmlspecialchars($user_email) ?></div>
        </div>
        <div class="online-dot"></div>
      </a>

      <a href="dashboard.php"><span>📊</span> Dashboard</a>
      <a href="#"><span>📚</span> My Courses</a>
      <a href="#"><span>💳</span> Transactions</a>
      <a href="settings.php"><span>⚙️</span> Settings</a>
      <a href="profile.php" class="active"><span>👤</span> Profile</a>
      <a href="logout.php" style="margin-top:auto;color:#ff4757;"><span>🚪</span> Log Out</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="profile-page">

        <!-- Hero Section -->
        <div class="profile-hero">
          <div class="profile-big-avatar">
            <?= $initials ?>
            <div class="status-badge"></div>
          </div>
          <div class="profile-hero-info">
            <h1>My Profile</h1>
            <div class="profile-email-badge">
              ✉️ <?= htmlspecialchars($user_email) ?>
            </div>
            <div class="profile-meta">
              <span class="profile-meta-item">🟢 Online</span>
              <span class="profile-meta-item">📅 Member since <?= $member_since ?></span>
              <span class="profile-meta-item">🆔 User #<?= $user_id ?></span>
            </div>
          </div>
        </div>

        <!-- Stats Row -->
        <div class="profile-stats">
          <div class="stat-card">
            <div class="stat-icon">📧</div>
            <div class="stat-value"><?= htmlspecialchars(substr($user_email, 0, strpos($user_email, '@'))) ?></div>
            <div class="stat-label">Username</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🎓</div>
            <div class="stat-value"><?= $purchases_res->num_rows ?></div>
            <div class="stat-label">Courses Enrolled</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🆔</div>
            <div class="stat-value">#<?= $user_id ?></div>
            <div class="stat-label">User ID</div>
          </div>
        </div>

        <!-- Info Grid -->
        <div class="profile-grid">
          <div class="info-card">
            <h3>📋 Account Details</h3>
            <div class="info-row">
              <span class="info-label">Email</span>
              <span class="info-value"><?= htmlspecialchars($user_email) ?></span>
            </div>
            <div class="info-row">
              <span class="info-label">User ID</span>
              <span class="info-value">#<?= $user_id ?></span>
            </div>
            <div class="info-row">
              <span class="info-label">Account Status</span>
              <span class="info-value" style="color:#4ade80;">✔ Active</span>
            </div>
            <div class="info-row">
              <span class="info-label">Member Since</span>
              <span class="info-value"><?= $member_since ?></span>
            </div>
          </div>

          <div class="info-card">
            <h3>🔐 Security</h3>
            <div class="info-row">
              <span class="info-label">Password</span>
              <span class="info-value">••••••••</span>
            </div>
            <div class="info-row">
              <span class="info-label">Login Method</span>
              <span class="info-value">Email & Password</span>
            </div>
            <div class="info-row">
              <span class="info-label">Session</span>
              <span class="info-value" style="color:#4ade80;">✔ Active</span>
            </div>
          </div>
        </div>

        <!-- Purchased Courses -->
        <div class="courses-card">
          <h3>🎓 Enrolled Courses</h3>
          <?php if ($purchases_res->num_rows === 0): ?>
            <p class="no-courses">No courses enrolled yet. <a href="dashboard.php" style="color:#00f2fe;">Browse courses →</a></p>
          <?php else: ?>
            <?php
              // Reset pointer
              $purchases_res->data_seek(0);
              while ($row = $purchases_res->fetch_assoc()):
            ?>
            <div class="course-item">
              <div class="course-dot"></div>
              <span class="course-name"><?= htmlspecialchars(str_replace('_', ' ', ucwords($row['course'], '_'))) ?></span>
              <span class="course-date"><?= date('M j, Y', strtotime($row['purchase_date'])) ?></span>
            </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>

        <!-- Logout -->
        <a href="logout.php" class="logout-btn">🚪 Log Out of My Account</a>

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
