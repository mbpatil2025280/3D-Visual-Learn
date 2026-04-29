<?php
session_start();

if (!isset($_SESSION['user_email'])) {
  header("Location: index.html");
  exit();
}

include 'db.php';

$user_email = $_SESSION['user_email'];
$user_id    = $_SESSION['user_id'];

$success_msg = '';
$error_msg   = '';

// ── Handle Change Password ───────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
  $current  = $_POST['current_password'];
  $new_pass = $_POST['new_password'];
  $confirm  = $_POST['confirm_password'];

  $sql    = "SELECT * FROM users WHERE id='$user_id' AND password='$current'";
  $result = $conn->query($sql);

  if ($result->num_rows === 0) {
    $error_msg = 'Current password is incorrect.';
  } elseif ($new_pass !== $confirm) {
    $error_msg = 'New passwords do not match.';
  } elseif (strlen($new_pass) < 6) {
    $error_msg = 'New password must be at least 6 characters.';
  } else {
    $conn->query("UPDATE users SET password='$new_pass' WHERE id='$user_id'");
    $success_msg = 'Password updated successfully!';
  }
}

// ── Handle Delete Account ────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
  $confirm_delete = $_POST['confirm_delete'] ?? '';
  if ($confirm_delete === 'DELETE') {
    $conn->query("DELETE FROM users WHERE id='$user_id'");
    session_destroy();
    echo "<script>
      alert('Your account has been permanently deleted.');
      window.location.href='index.html';
    </script>";
    exit();
  } else {
    $error_msg = 'Please type DELETE to confirm account deletion.';
  }
}

// Avatar
$initials = strtoupper(substr($user_email, 0, 2));
$colors   = ['#00f2fe','#4facfe','#a855f7','#f472b6','#34d399','#fb923c','#60a5fa'];
$color    = $colors[abs(crc32($user_email)) % count($colors)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | 3D Visual Learning</title>
  <link rel="stylesheet" href="style.css">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#00f2fe">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <style>
    :root { --avatar-color: <?= $color ?>; }
    body { display: block; text-align: left; font-family: 'Outfit', sans-serif; }

    /* ── Sidebar profile ── */
    .sidebar-profile {
      display: flex; align-items: center; gap: 10px;
      padding: 14px 16px; margin: 0 0 12px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.09);
      border-radius: 14px; text-decoration: none;
      transition: all 0.25s ease;
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
    .online-dot { width: 8px; height: 8px; background: #4ade80; border-radius: 50%; margin-left: auto; flex-shrink: 0; box-shadow: 0 0 6px #4ade8099; animation: pd 2s ease-in-out infinite; }
    @keyframes pd { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:0.6;} }

    /* ── Settings Page Layout ── */
    .settings-page { max-width: 820px; margin: 0 auto; padding: 10px 0 60px; }

    /* ── Page header ── */
    .settings-header {
      display: flex; align-items: center; gap: 18px;
      margin-bottom: 36px;
    }
    .settings-header-icon {
      width: 54px; height: 54px; border-radius: 16px;
      background: linear-gradient(135deg, var(--avatar-color)22, var(--avatar-color)44);
      border: 1px solid var(--avatar-color)44;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem;
      box-shadow: 0 0 20px var(--avatar-color)22;
    }
    .settings-header h1 {
      font-size: 2rem; font-weight: 900; margin: 0;
      background: linear-gradient(to right, #fff, #94a3b8);
      -webkit-background-clip: text; background-clip: text;
      -webkit-text-fill-color: transparent;
      filter: none; letter-spacing: 0;
    }
    .settings-header p { font-size: 0.88rem; color: #64748b; margin-top: 4px; }

    /* ── Settings Tabs ── */
    .settings-tabs {
      display: flex; gap: 6px; margin-bottom: 28px;
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.07);
      border-radius: 14px; padding: 6px;
      flex-wrap: wrap;
    }
    .tab-btn {
      flex: 1; min-width: 100px; padding: 10px 16px;
      border-radius: 10px; border: none; cursor: pointer;
      font-family: 'Outfit', sans-serif;
      font-size: 0.85rem; font-weight: 600;
      background: transparent; color: #64748b;
      transition: all 0.25s ease; display: flex;
      align-items: center; justify-content: center; gap: 7px;
      width: auto;
    }
    .tab-btn:hover { color: #e2e8f0; background: rgba(255,255,255,0.05); }
    .tab-btn.active {
      background: linear-gradient(135deg, var(--avatar-color)22, rgba(79,172,254,0.15));
      color: var(--avatar-color);
      border: 1px solid var(--avatar-color)33;
      box-shadow: 0 2px 12px var(--avatar-color)22;
    }

    /* ── Settings Panels ── */
    .settings-panel { display: none; }
    .settings-panel.active { display: block; animation: panelIn 0.3s ease; }
    @keyframes panelIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }

    /* ── Section Card ── */
    .settings-section {
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.07);
      border-radius: 20px; padding: 28px 28px;
      margin-bottom: 20px; transition: border-color 0.3s;
    }
    .settings-section:hover { border-color: rgba(255,255,255,0.12); }

    .section-title {
      font-size: 0.82rem; font-weight: 700;
      color: #94a3b8; text-transform: uppercase;
      letter-spacing: 0.9px; margin-bottom: 22px;
      display: flex; align-items: center; gap: 10px;
    }
    .section-title::after {
      content: ''; flex: 1; height: 1px;
      background: rgba(255,255,255,0.07);
    }

    /* ── Form fields ── */
    .field-group { margin-bottom: 20px; }
    .field-label {
      display: block; font-size: 0.85rem;
      font-weight: 600; color: #cbd5e1;
      margin-bottom: 8px;
    }
    .field-hint { font-size: 0.76rem; color: #475569; margin-top: 6px; }
    .settings-input {
      display: block; width: 100%; padding: 13px 16px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 12px; color: #e2e8f0;
      font-size: 0.92rem; font-family: 'Outfit', sans-serif;
      outline: none; transition: all 0.25s ease;
      margin: 0;
    }
    .settings-input:focus {
      border-color: var(--avatar-color);
      background: rgba(255,255,255,0.07);
      box-shadow: 0 0 0 3px var(--avatar-color)22;
    }
    .settings-input:disabled {
      opacity: 0.45; cursor: not-allowed;
    }
    .settings-input::placeholder { color: #475569; }

    /* ── Save / Action Buttons ── */
    .btn-primary {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 13px 28px; border-radius: 12px; border: none;
      font-family: 'Outfit', sans-serif;
      font-size: 0.92rem; font-weight: 700; cursor: pointer;
      background: linear-gradient(135deg, var(--avatar-color), #4facfe);
      color: #020617; transition: all 0.3s ease;
      width: auto;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--avatar-color)44; filter: brightness(1.1); }

    .btn-danger {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 13px 28px; border-radius: 12px; border: none;
      font-family: 'Outfit', sans-serif;
      font-size: 0.92rem; font-weight: 700; cursor: pointer;
      background: rgba(255,71,87,0.12);
      border: 1px solid rgba(255,71,87,0.3);
      color: #ff4757; transition: all 0.3s ease; width: auto;
    }
    .btn-danger:hover { background: rgba(255,71,87,0.22); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,71,87,0.25); }

    .btn-secondary {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 13px 28px; border-radius: 12px;
      font-family: 'Outfit', sans-serif;
      font-size: 0.92rem; font-weight: 600; cursor: pointer;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      color: #94a3b8; text-decoration: none;
      transition: all 0.3s ease; width: auto;
    }
    .btn-secondary:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; transform: translateY(-1px); }

    /* ── Toggle Switch ── */
    .toggle-row {
      display: flex; align-items: center;
      justify-content: space-between;
      padding: 14px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .toggle-row:last-child { border-bottom: none; }
    .toggle-info {}
    .toggle-title { font-size: 0.92rem; font-weight: 600; color: #e2e8f0; }
    .toggle-desc  { font-size: 0.78rem; color: #64748b; margin-top: 3px; }

    .toggle-switch { position: relative; width: 50px; height: 26px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute; inset: 0;
      background: rgba(255,255,255,0.1);
      border-radius: 50px; cursor: pointer;
      transition: 0.3s; border: 1px solid rgba(255,255,255,0.1);
    }
    .slider:before {
      content: ''; position: absolute;
      width: 18px; height: 18px; background: #fff;
      border-radius: 50%; left: 3px; top: 3px;
      transition: 0.3s; box-shadow: 0 1px 4px rgba(0,0,0,0.4);
    }
    input:checked + .slider { background: var(--avatar-color); border-color: var(--avatar-color); }
    input:checked + .slider:before { transform: translateX(24px); }

    /* ── Color swatches ── */
    .color-swatches {
      display: flex; gap: 12px; flex-wrap: wrap; margin-top: 4px;
    }
    .swatch {
      width: 36px; height: 36px; border-radius: 50%;
      border: 3px solid transparent; cursor: pointer;
      transition: all 0.25s ease;
      position: relative;
    }
    .swatch:hover { transform: scale(1.2); }
    .swatch.selected { border-color: #fff; box-shadow: 0 0 0 2px rgba(255,255,255,0.3); }
    .swatch::after {
      content: '✓'; position: absolute;
      inset: 0; display: flex; align-items: center;
      justify-content: center; color: #fff;
      font-weight: 900; font-size: 0.8rem;
      opacity: 0; transition: 0.2s;
    }
    .swatch.selected::after { opacity: 1; }

    /* ── Alert/notification messages ── */
    .alert {
      padding: 14px 20px; border-radius: 12px;
      font-size: 0.9rem; font-weight: 600;
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 20px; animation: panelIn 0.3s ease;
    }
    .alert-success {
      background: rgba(74,222,128,0.1);
      border: 1px solid rgba(74,222,128,0.3);
      color: #4ade80;
    }
    .alert-error {
      background: rgba(255,71,87,0.1);
      border: 1px solid rgba(255,71,87,0.3);
      color: #ff4757;
    }

    /* ── Danger zone ── */
    .danger-zone {
      background: rgba(255,71,87,0.04);
      border: 1px solid rgba(255,71,87,0.15);
      border-radius: 20px; padding: 28px;
      margin-bottom: 20px;
    }
    .danger-zone .section-title { color: #ff4757; }
    .danger-zone .section-title::after { background: rgba(255,71,87,0.15); }

    /* ── Info badge in account ── */
    .info-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(0,242,254,0.07);
      border: 1px solid rgba(0,242,254,0.2);
      border-radius: 8px; padding: 10px 14px;
      font-size: 0.85rem; color: #94a3b8;
      width: 100%;
    }
    .info-badge strong { color: #00f2fe; }

    /* ── Responsive ── */
    @media (max-width: 600px) {
      .settings-tabs { overflow-x: auto; flex-wrap: nowrap; }
      .tab-btn { white-space: nowrap; flex: none; }
    }
  </style>
</head>
<body>

<div class="admin-layout">

  <!-- ═══ Sidebar ═══ -->
  <aside class="sidebar">
    <div class="logo">🎓 3D Visual Learn</div>

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
    <a href="settings.php" class="active"><span>⚙️</span> Settings</a>
    <a href="profile.php"><span>👤</span> Profile</a>
    <a href="logout.php" style="margin-top:auto;color:#ff4757;"><span>🚪</span> Log Out</a>
  </aside>

  <!-- ═══ Main Content ═══ -->
  <main class="main-content">
    <div class="settings-page">

      <!-- Page Header -->
      <div class="settings-header">
        <div class="settings-header-icon">⚙️</div>
        <div>
          <h1>Settings</h1>
          <p>Manage your account preferences and app configuration</p>
        </div>
      </div>

      <!-- Flash Messages -->
      <?php if ($success_msg): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success_msg) ?></div>
      <?php endif; ?>
      <?php if ($error_msg): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error_msg) ?></div>
      <?php endif; ?>

      <!-- Tab Navigation -->
      <div class="settings-tabs" role="tablist">
        <button class="tab-btn active" onclick="switchTab('account',  this)" role="tab">👤 Account</button>
        <button class="tab-btn"        onclick="switchTab('security', this)" role="tab">🔐 Security</button>
        <button class="tab-btn"        onclick="switchTab('appearance',this)" role="tab">🎨 Appearance</button>
        <button class="tab-btn"        onclick="switchTab('notifications',this)" role="tab">🔔 Notifications</button>
        <button class="tab-btn"        onclick="switchTab('privacy',  this)" role="tab">🛡️ Privacy</button>
      </div>

      <!-- ════════════ TAB: Account ════════════ -->
      <div id="tab-account" class="settings-panel active">

        <!-- Current Info -->
        <div class="settings-section">
          <div class="section-title">📋 Account Information</div>

          <div class="field-group">
            <label class="field-label">Email Address</label>
            <div class="info-badge">
              ✉️ <strong><?= htmlspecialchars($user_email) ?></strong>
            </div>
            <p class="field-hint">Your login email address. Contact support to change it.</p>
          </div>

          <div class="field-group">
            <label class="field-label">User ID</label>
            <div class="info-badge">🆔 <strong>#<?= $user_id ?></strong></div>
          </div>

          <div class="field-group">
            <label class="field-label">Avatar / Profile Photo</label>
            <div style="display:flex;align-items:center;gap:20px;margin-top:6px;">
              <div style="width:64px;height:64px;border-radius:50%;background:var(--avatar-color);color:#fff;font-size:1.5rem;font-weight:900;display:flex;align-items:center;justify-content:center;box-shadow:0 0 20px var(--avatar-color)55;">
                <?= $initials ?>
              </div>
              <div>
                <p style="font-size:0.88rem;color:#e2e8f0;font-weight:600;">Auto-generated avatar</p>
                <p style="font-size:0.78rem;color:#64748b;margin-top:4px;">Your avatar is automatically generated from your email initials with a unique colour.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Logout all devices -->
        <div class="settings-section">
          <div class="section-title">🔌 Sessions</div>
          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Current Session</div>
              <div class="toggle-desc">You are currently logged in on this device.</div>
            </div>
            <span style="color:#4ade80;font-weight:700;font-size:0.85rem;">● Active</span>
          </div>
          <div style="margin-top:20px;">
            <a href="logout.php" class="btn-secondary">🚪 Log Out of Account</a>
          </div>
        </div>

      </div><!-- /tab-account -->

      <!-- ════════════ TAB: Security ════════════ -->
      <div id="tab-security" class="settings-panel">

        <div class="settings-section">
          <div class="section-title">🔑 Change Password</div>

          <form method="POST" action="settings.php#security" id="changePasswordForm">
            <input type="hidden" name="action" value="change_password">

            <div class="field-group">
              <label class="field-label" for="current_password">Current Password</label>
              <input class="settings-input" type="password" id="current_password" name="current_password" placeholder="Enter your current password" required>
            </div>

            <div class="field-group">
              <label class="field-label" for="new_password">New Password</label>
              <input class="settings-input" type="password" id="new_password" name="new_password" placeholder="Minimum 6 characters" required>
            </div>

            <div class="field-group">
              <label class="field-label" for="confirm_password">Confirm New Password</label>
              <input class="settings-input" type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password" required>
              <p class="field-hint" id="pw-match-hint"> </p>
            </div>

            <!-- Password Strength -->
            <div class="field-group">
              <label class="field-label">Password Strength</label>
              <div style="height:6px;background:rgba(255,255,255,0.07);border-radius:99px;overflow:hidden;">
                <div id="strength-bar" style="height:100%;width:0%;border-radius:99px;transition:all 0.4s ease;background:#ff4757;"></div>
              </div>
              <p class="field-hint" id="strength-label">Enter a new password above</p>
            </div>

            <button type="submit" class="btn-primary">🔐 Update Password</button>
          </form>
        </div>

        <!-- Danger Zone -->
        <div class="danger-zone">
          <div class="section-title">⚠️ Danger Zone</div>

          <p style="font-size:0.88rem;color:#94a3b8;margin-bottom:20px;line-height:1.7;">
            Permanently delete your account and all associated data. <br>
            <strong style="color:#ff4757;">This action cannot be undone.</strong>
          </p>

          <form method="POST" action="settings.php" onsubmit="return confirmDelete()">
            <input type="hidden" name="action" value="delete_account">
            <div class="field-group">
              <label class="field-label">Type <strong style="color:#ff4757;">DELETE</strong> to confirm</label>
              <input class="settings-input" type="text" name="confirm_delete" id="confirm_delete" placeholder='Type DELETE here' autocomplete="off">
            </div>
            <button type="submit" class="btn-danger">🗑️ Delete My Account</button>
          </form>
        </div>

      </div><!-- /tab-security -->

      <!-- ════════════ TAB: Appearance ════════════ -->
      <div id="tab-appearance" class="settings-panel">

        <div class="settings-section">
          <div class="section-title">🎨 Theme</div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Dark Mode</div>
              <div class="toggle-desc">Use the dark theme across the app (recommended)</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" id="toggle-dark" checked onchange="savePref('theme', this.checked ? 'dark' : 'light')">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Animations</div>
              <div class="toggle-desc">Enable smooth UI transitions and animations</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" id="toggle-anim" checked onchange="savePref('animations', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Boot Screen</div>
              <div class="toggle-desc">Show the OS-style boot animation on startup</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" id="toggle-boot" checked onchange="savePref('bootScreen', this.checked)">
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <div class="settings-section">
          <div class="section-title">🌈 Accent Colour</div>
          <p style="font-size:0.85rem;color:#64748b;margin-bottom:16px;">Choose your preferred highlight colour across the app.</p>
          <div class="color-swatches" id="swatch-container">
            <div class="swatch <?= $color === '#00f2fe' ? 'selected' : '' ?>" style="background:#00f2fe;" data-color="#00f2fe" onclick="selectColor(this)" title="Cyan"></div>
            <div class="swatch <?= $color === '#4facfe' ? 'selected' : '' ?>" style="background:#4facfe;" data-color="#4facfe" onclick="selectColor(this)" title="Blue"></div>
            <div class="swatch <?= $color === '#a855f7' ? 'selected' : '' ?>" style="background:#a855f7;" data-color="#a855f7" onclick="selectColor(this)" title="Purple"></div>
            <div class="swatch <?= $color === '#f472b6' ? 'selected' : '' ?>" style="background:#f472b6;" data-color="#f472b6" onclick="selectColor(this)" title="Pink"></div>
            <div class="swatch <?= $color === '#34d399' ? 'selected' : '' ?>" style="background:#34d399;" data-color="#34d399" onclick="selectColor(this)" title="Green"></div>
            <div class="swatch <?= $color === '#fb923c' ? 'selected' : '' ?>" style="background:#fb923c;" data-color="#fb923c" onclick="selectColor(this)" title="Orange"></div>
            <div class="swatch <?= $color === '#60a5fa' ? 'selected' : '' ?>" style="background:#60a5fa;" data-color="#60a5fa" onclick="selectColor(this)" title="Sky Blue"></div>
            <div class="swatch" style="background:#f43f5e;" data-color="#f43f5e" onclick="selectColor(this)" title="Rose"></div>
            <div class="swatch" style="background:#fbbf24;" data-color="#fbbf24" onclick="selectColor(this)" title="Amber"></div>
          </div>
          <p class="field-hint" style="margin-top:14px;">Accent colour is saved locally in your browser.</p>
        </div>

        <div class="settings-section">
          <div class="section-title">🔡 Font Size</div>
          <div style="display:flex;align-items:center;gap:16px;">
            <span style="font-size:0.78rem;color:#64748b;">Small</span>
            <input type="range" id="font-scale" min="90" max="120" value="100" step="5"
              style="flex:1;accent-color:var(--avatar-color);"
              oninput="applyFontScale(this.value)">
            <span style="font-size:0.78rem;color:#64748b;">Large</span>
            <span id="font-scale-label" style="font-size:0.82rem;color:var(--avatar-color);font-weight:700;min-width:34px;">100%</span>
          </div>
        </div>

      </div><!-- /tab-appearance -->

      <!-- ════════════ TAB: Notifications ════════════ -->
      <div id="tab-notifications" class="settings-panel">

        <div class="settings-section">
          <div class="section-title">📬 Email Notifications</div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Course Updates</div>
              <div class="toggle-desc">Get notified when new lessons are added to your courses</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked onchange="savePref('notif_course', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Subscription Reminders</div>
              <div class="toggle-desc">Reminders before your subscription expires</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked onchange="savePref('notif_sub', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Promotional Offers</div>
              <div class="toggle-desc">Discounts and special offers on courses</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" onchange="savePref('notif_promo', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Security Alerts</div>
              <div class="toggle-desc">Notify on new logins or password changes</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked onchange="savePref('notif_security', this.checked)">
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <div class="settings-section">
          <div class="section-title">🔔 In-App Notifications</div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Achievement Badges</div>
              <div class="toggle-desc">Show a notification when you earn a badge</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked onchange="savePref('notif_badge', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">System Announcements</div>
              <div class="toggle-desc">Important updates from the 3D Visual Learn team</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked onchange="savePref('notif_system', this.checked)">
              <span class="slider"></span>
            </label>
          </div>
        </div>

      </div><!-- /tab-notifications -->

      <!-- ════════════ TAB: Privacy ════════════ -->
      <div id="tab-privacy" class="settings-panel">

        <div class="settings-section">
          <div class="section-title">🛡️ Privacy Controls</div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Show Profile to Others</div>
              <div class="toggle-desc">Allow other users to view your basic profile info</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" onchange="savePref('profile_public', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Show Enrolled Courses</div>
              <div class="toggle-desc">Display which courses you're enrolled in publicly</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" onchange="savePref('show_courses', this.checked)">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Data Analytics</div>
              <div class="toggle-desc">Allow anonymous usage data to help improve the platform</div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked onchange="savePref('analytics', this.checked)">
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <div class="settings-section">
          <div class="section-title">📂 My Data</div>
          <p style="font-size:0.88rem;color:#94a3b8;margin-bottom:20px;line-height:1.7;">
            You can request a copy of all data we hold about you, or delete your account entirely.
          </p>
          <div style="display:flex;gap:14px;flex-wrap:wrap;">
            <button onclick="showToast('📦 Data export has been requested. You will receive an email shortly.')" class="btn-secondary">📦 Export My Data</button>
            <a href="#" onclick="switchTab('security', document.querySelectorAll('.tab-btn')[1]); return false;" class="btn-danger" style="text-decoration:none;">🗑️ Delete Account</a>
          </div>
        </div>

      </div><!-- /tab-privacy -->

    </div><!-- /.settings-page -->
  </main>
</div>

<!-- ════ Toast Notification ════ -->
<div id="toast" style="
  position:fixed; bottom:30px; right:30px; z-index:9999;
  background:rgba(15,23,42,0.95); border:1px solid rgba(255,255,255,0.12);
  border-radius:14px; padding:16px 22px;
  font-size:0.88rem; color:#e2e8f0; font-weight:600;
  box-shadow: 0 12px 40px rgba(0,0,0,0.4);
  transform:translateY(80px); opacity:0;
  transition:all 0.4s cubic-bezier(0.22,1,0.36,1);
  max-width:340px; display:flex; align-items:center; gap:10px;
  backdrop-filter: blur(20px);
">
</div>

<script>
  // ── Tab switching ─────────────────────────────────────────
  function switchTab(name, btn) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');

    // If opening security tab and there was a server-side form submission, scroll
    if (name === 'security') {
      setTimeout(() => {
        document.getElementById('tab-security').scrollIntoView({ behavior:'smooth', block:'start' });
      }, 100);
    }
  }

  // ── Auto-open security tab if password form was submitted ─
  <?php if (isset($_POST['action']) && $_POST['action'] === 'change_password'): ?>
  window.addEventListener('DOMContentLoaded', () => {
    switchTab('security', document.querySelectorAll('.tab-btn')[1]);
  });
  <?php endif; ?>

  // ── Save preference to localStorage ──────────────────────
  function savePref(key, value) {
    localStorage.setItem('pref_' + key, JSON.stringify(value));
    showToast('✅ Preference saved!');
  }

  // ── Load saved prefs on page load ────────────────────────
  window.addEventListener('DOMContentLoaded', () => {
    loadPrefs();
    loadFontScale();
    loadAccentColor();
  });

  function loadPrefs() {
    const map = {
      'pref_theme':        'toggle-dark',
      'pref_animations':   'toggle-anim',
      'pref_bootScreen':   'toggle-boot',
    };
    Object.entries(map).forEach(([key, id]) => {
      const val = localStorage.getItem(key);
      if (val !== null) {
        const el = document.getElementById(id);
        if (el) el.checked = JSON.parse(val);
      }
    });
  }

  // ── Password strength indicator ───────────────────────────
  document.getElementById('new_password').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-label');

    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;

    const levels = [
      { pct:'0%',  color:'#ff4757', label:'Too short' },
      { pct:'20%', color:'#ff4757', label:'Very Weak' },
      { pct:'40%', color:'#fbbc04', label:'Weak' },
      { pct:'60%', color:'#fbbf24', label:'Fair' },
      { pct:'80%', color:'#34d399', label:'Strong' },
      { pct:'100%',color:'#4ade80', label:'Very Strong 💪' },
    ];
    const lv = levels[score] || levels[0];
    bar.style.width  = val.length === 0 ? '0%' : lv.pct;
    bar.style.background = lv.color;
    lbl.textContent  = val.length === 0 ? 'Enter a new password above' : lv.label;
  });

  // ── Confirm password match hint ───────────────────────────
  document.getElementById('confirm_password').addEventListener('input', function() {
    const np  = document.getElementById('new_password').value;
    const hint = document.getElementById('pw-match-hint');
    if (this.value.length === 0) { hint.textContent = ' '; hint.style.color = ''; return; }
    if (this.value === np) {
      hint.textContent = '✅ Passwords match';
      hint.style.color = '#4ade80';
    } else {
      hint.textContent = '❌ Passwords do not match';
      hint.style.color = '#ff4757';
    }
  });

  // ── Delete account confirm ────────────────────────────────
  function confirmDelete() {
    const val = document.getElementById('confirm_delete').value;
    if (val !== 'DELETE') {
      showToast('⚠️ Please type DELETE exactly to confirm.');
      return false;
    }
    return confirm('⚠️ Are you absolutely sure? This CANNOT be undone!');
  }

  // ── Accent colour swatches ────────────────────────────────
  function selectColor(el) {
    document.querySelectorAll('.swatch').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');
    const c = el.dataset.color;
    document.documentElement.style.setProperty('--avatar-color', c);
    localStorage.setItem('pref_accentColor', c);
    showToast('🎨 Accent colour updated!');
  }

  function loadAccentColor() {
    const saved = localStorage.getItem('pref_accentColor');
    if (saved) {
      document.documentElement.style.setProperty('--avatar-color', saved);
      document.querySelectorAll('.swatch').forEach(s => {
        s.classList.toggle('selected', s.dataset.color === saved);
      });
    }
  }

  // ── Font Scale ────────────────────────────────────────────
  function applyFontScale(val) {
    document.body.style.fontSize = (val / 100) + 'rem';
    document.getElementById('font-scale-label').textContent = val + '%';
    localStorage.setItem('pref_fontScale', val);
  }

  function loadFontScale() {
    const saved = localStorage.getItem('pref_fontScale');
    if (saved) {
      document.getElementById('font-scale').value = saved;
      applyFontScale(saved);
    }
  }

  // ── Toast notification ────────────────────────────────────
  let toastTimer;
  function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.innerHTML = msg;
    toast.style.transform = 'translateY(0)';
    toast.style.opacity   = '1';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toast.style.transform = 'translateY(80px)';
      toast.style.opacity   = '0';
    }, 3000);
  }

  // Service Worker
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('service-worker.js'));
  }
</script>
</body>
</html>
