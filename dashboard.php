<?php
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user'])) { 
  header('Location: login.php'); 
  exit; 
}
$user  = $_SESSION['user'];
$theme = $_GET['theme'] ?? ($_COOKIE['ui_theme'] ?? 'light');

if (in_array($theme, ['light','dark'])) setcookie('ui_theme', $theme, time()+60*60*24*30, '/');
$dataFile = 'queue_data.json';

$patients = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$total   = count($patients);
$waiting = count(array_filter($patients, fn($p) => $p['status']==='Waiting'));

$deptCounts = [];
foreach ($patients as $p) $deptCounts[$p['dept']] = ($deptCounts[$p['dept']] ?? 0) + 1;
arsort($deptCounts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Dashboard – CareQueue</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="app-page <?=($theme==='dark'?'dark':'')?>">

  <header class="site-header">
    <div class="brand">Care<em>Queue</em></div>
    <span class="nav-user">Hello, <strong><?=htmlspecialchars($user['fname'])?></strong></span>
    <a href="dashboard.php?theme=<?=($theme==='dark'?'light':'dark')?>" class="theme-toggle">
      <i class="fa-solid <?=($theme==='dark'?'fa-sun':'fa-moon')?>"></i>
      <?=($theme==='dark'?'Light':'Dark')?>
    </a>
    <a href="index.php" class="nav-btn"><i class="fa-solid fa-user-plus"></i> Register Patient</a>
    <a href="logout.php" class="nav-btn danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </header>

  <main class="main">

    <div class="greeting">
      <h2><i class="fa-solid fa-gauge-high" style="font-size:1.4rem;"></i> Dashboard</h2>
      <p>Welcome back, <?=htmlspecialchars($user['fname'])?>. Here's today's queue overview.</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card sc-1">
        <i class="fa-solid fa-users stat-icon"></i>
        <div class="num"><?=$total?></div>
        <div class="lbl">Total Patients</div>
      </div>
      <div class="stat-card sc-2">
        <i class="fa-solid fa-clock stat-icon"></i>
        <div class="num"><?=$waiting?></div>
        <div class="lbl">Currently Waiting</div>
      </div>
      <div class="stat-card sc-3">
        <i class="fa-solid fa-circle-check stat-icon"></i>
        <div class="num"><?=$total-$waiting?></div>
        <div class="lbl">Served / Other</div>
      </div>
      <div class="stat-card sc-4">
        <i class="fa-solid fa-building-columns stat-icon"></i>
        <div class="num"><?=count($deptCounts)?></div>
        <div class="lbl">Departments Active</div>
      </div>
    </div>

    <div class="content-grid">

      <!-- Queue Table -->
      <div class="panel">
        <div class="panel-head">
          <h3><i class="fa-solid fa-table-list"></i> Current Queue</h3>
          <span style="font-size:.8rem;color:var(--muted);"><?=$total?> total</span>
        </div>
        <div class="panel-body p0">
          <?php if(empty($patients)): ?>
          <p class="no-data">
            <i class="fa-solid fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.4;"></i>
            No patients registered yet.
          </p>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th><i class="fa-solid fa-hashtag fa-xs"></i> Queue #</th>
                <th><i class="fa-solid fa-user fa-xs"></i> Name</th>
                <th><i class="fa-solid fa-phone fa-xs"></i> Mobile</th>
                <th><i class="fa-solid fa-building fa-xs"></i> Dept</th>
                <th><i class="fa-solid fa-clock fa-xs"></i> Time</th>
                <th><i class="fa-solid fa-circle-dot fa-xs"></i> Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach(array_reverse($patients) as $p): ?>
              <tr>
                <td class="qno"><?=htmlspecialchars($p['queue_no'])?></td>
                <td><?=htmlspecialchars($p['name'])?></td>
                <td><?=htmlspecialchars($p['mobile'])?></td>
                <td><span class="badge b-dept"><?=htmlspecialchars($p['dept'])?></span></td>
                <td style="color:var(--muted);font-size:.82rem;"><?=htmlspecialchars($p['time'])?></td>
                <td><span class="badge <?=$p['status']==='Waiting'?'b-wait':'b-done'?>"><?=htmlspecialchars($p['status'])?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="sidebar">

        <div class="panel">
          <div class="panel-head"><h3><i class="fa-solid fa-building-user"></i> By Department</h3></div>
          <div class="panel-body">
            <?php if(empty($deptCounts)): ?>
            <p style="color:var(--muted);font-size:.85rem;">No data yet.</p>
            <?php else: ?>
            <?php foreach($deptCounts as $dept=>$cnt): ?>
            <div class="dept-row">
              <div class="dept-meta">
                <span class="dept-name"><?=htmlspecialchars($dept)?></span>
                <span class="dept-count"><?=$cnt?> patient<?=$cnt>1?'s':''?></span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width:<?=$total?round($cnt/$total*100):0?>%"></div></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="action-card">
          <p>Register a new patient to the queue</p>
          <a href="index.php" class="action-btn">
            <i class="fa-solid fa-user-plus"></i> Register Patient
          </a>
        </div>

        <div class="panel">
          <div class="panel-head"><h3><i class="fa-solid fa-key"></i> Session Info</h3></div>
          <div class="panel-body">
            <div class="session-box">
              <p><i class="fa-solid fa-circle-info fa-xs"></i> Active Session</p>
              <div class="srow"><span>User ID</span><span>#<?=$user['id']?></span></div>
              <div class="srow"><span>Email</span><span><?=htmlspecialchars($user['email'])?></span></div>
              <div class="srow"><span>Login Time</span><span><?=$_SESSION['login_time']??'—'?></span></div>
              <div class="srow"><span>Theme Cookie</span><span><?=htmlspecialchars($theme)?></span></div>
              <div class="srow">
                <span>Remember Cookie</span>
                <span>
                  <?php if(isset($_COOKIE['remember_email'])): ?>
                    <i class="fa-solid fa-circle-check" style="color:#5cc876;"></i> Active
                  <?php else: ?>
                    <i class="fa-solid fa-circle-xmark" style="color:#d4708e;"></i> None
                  <?php endif; ?>
                </span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </main>

</body>
</html>