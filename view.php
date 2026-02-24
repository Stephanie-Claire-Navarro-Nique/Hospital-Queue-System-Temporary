<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Register Patient – CareQueue</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="app-page <?=($theme==='dark'?'dark':'')?>">

  <header class="site-header">
    <div class="brand">Care<em>Queue</em></div>
    <a href="index.php?theme=<?=($theme==='dark'?'light':'dark')?>" class="theme-toggle">
      <i class="fa-solid <?=($theme==='dark'?'fa-sun':'fa-moon')?>"></i>
      <?=($theme==='dark'?'Light':'Dark')?>
    </a>
    <a href="dashboard.php" class="nav-btn"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="logout.php" class="nav-btn danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </header>

  <main class="main narrow">
    <h2 class="page-title"><i class="fa-solid fa-user-plus" style="font-size:1.3rem;"></i> Patient Registration</h2>
    <p class="page-sub">Fill in the form to assign a queue number to a new patient.</p>

    <?php if ($success): ?>
    <div class="queue-banner">
      <p><i class="fa-solid fa-ticket"></i> Queue Number Assigned</p>
      <div class="qnum"><?= $success ?></div>
      <small><i class="fa-solid fa-bell"></i> Please wait for your number to be called.</small>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert-err">
      <ul><?php foreach($errors as $e):?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul>
    </div>
    <?php endif; ?>

    <div class="register-grid">

      <!-- Form -->
      <div class="panel">
        <div class="panel-head"><h3><i class="fa-solid fa-pen-to-square"></i> New Patient</h3></div>
        <div class="panel-body p24">
          <form method="POST" action="controller.php">
            <div class="field">
              <label><i class="fa-solid fa-user fa-xs"></i> Full Name</label>
              <input type="text" name="name" placeholder="e.g. Juan Dela Cruz" value="<?=htmlspecialchars($name)?>"/>
              <p class="hint">Letters only · minimum 3 characters</p>
            </div>
            <div class="field">
              <label><i class="fa-solid fa-mobile-screen fa-xs"></i> Mobile Number</label>
              <input type="text" name="mobile" placeholder="09XXXXXXXXX" maxlength="11" value="<?=htmlspecialchars($mobile)?>"/>
              <p class="hint">Must start with 09 · 11 digits total</p>
            </div>
            <div class="field">
              <label><i class="fa-solid fa-building fa-xs"></i> Department</label>
              <select name="dept">
                <option value="">— Select Department —</option>
                <option value="ER"    <?=$dept=='ER'   ?'selected':''?>>Emergency Room</option>
                <option value="OPD"   <?=$dept=='OPD'  ?'selected':''?>>Out-Patient</option>
                <option value="Pedia" <?=$dept=='Pedia'?'selected':''?>>Pediatrics</option>
                <option value="Cardio"<?=$dept=='Cardio'?'selected':''?>>Cardiology</option>
              </select>
            </div>
            <button type="submit" class="btn-main">
              <i class="fa-solid fa-ticket"></i> Get Queue Number
            </button>
          </form>
        </div>
      </div>

      <!-- Queue List -->
      <div class="panel">
        <div class="panel-head">
          <h3><i class="fa-solid fa-table-list"></i> Queue List</h3>
          <span style="font-size:.8rem;color:var(--muted);"><?=count($patients)?> total</span>
        </div>
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
              <th><i class="fa-solid fa-building fa-xs"></i> Dept</th>
              <th><i class="fa-solid fa-circle-dot fa-xs"></i> Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($patients as $p): ?>
            <tr>
              <td class="qno"><?=htmlspecialchars($p['queue_no'])?></td>
              <td><?=htmlspecialchars($p['name'])?></td>
              <td><?=htmlspecialchars($p['dept'])?></td>
              <td><span class="badge b-wait"><?=htmlspecialchars($p['status'])?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>

    </div>
  </main>

</body>
</html>