<?php
session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['user'])) { header('Location: dashboard.php'); exit; }

$errors = []; $success = false; $fname = ''; $email = '';
$usersFile = 'users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    if (!preg_match('/^[A-Za-z\s]{3,}$/', $fname)) $errors[] = "Full name must be letters only and at least 3 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
    elseif (array_search($email, array_column($users, 'email')) !== false) $errors[] = "That email is already registered.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (empty($errors)) {
        $users[] = ['id'=>count($users)+1,'fname'=>$fname,'email'=>$email,'password'=>password_hash($password,PASSWORD_DEFAULT),'created'=>date('Y-m-d H:i:s')];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        $success = true; $fname = $email = '';
    }
}
$theme = $_COOKIE['ui_theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Sign Up – CareQueue</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="auth-page signup-page <?=($theme==='dark'?'dark':'')?>">

  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <header class="site-header auth-header">
    <div class="brand">Care<em>Queue</em></div>
    <a href="login.php" class="header-pill">
      <i class="fa-solid fa-arrow-left"></i> Back to Login
    </a>
  </header>

  <div class="auth-wrap wide">
    <div class="gradient-bar alt"></div>
    <div class="auth-card">
      <div class="tag-row">
        <span class="tag t-mint"><i class="fa-solid fa-hospital fa-xs"></i> Hospital</span>
        <span class="tag t-rose"><i class="fa-solid fa-list-ol fa-xs"></i> Queue System</span>
        <span class="tag t-lilac"><i class="fa-solid fa-id-badge fa-xs"></i> Staff Portal</span>
      </div>
      <h1 class="auth-title">Create Account</h1>
      <p class="auth-sub">Register to manage the hospital queue</p>

      <?php if ($success): ?>
      <div class="alert-ok">
        <i class="fa-solid fa-circle-check"></i> Account created! <a href="login.php">Login now <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
      <div class="alert-err">
        <ul><?php foreach($errors as $e):?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul>
      </div>
      <?php endif; ?>

      <form method="POST" action="signup.php">
        <div class="field">
          <label><i class="fa-solid fa-user fa-xs"></i> Full Name</label>
          <input type="text" name="fname" placeholder="e.g. Maria Santos" value="<?=htmlspecialchars($fname)?>"/>
          <p class="hint">Letters only · at least 3 characters</p>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-envelope fa-xs"></i> Email Address</label>
          <input type="email" name="email" placeholder="you@hospital.com" value="<?=htmlspecialchars($email)?>"/>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-lock fa-xs"></i> Password</label>
          <input type="password" name="password" placeholder="Minimum 6 characters"/>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-shield-halved fa-xs"></i> Confirm Password</label>
          <input type="password" name="confirm" placeholder="Repeat your password"/>
        </div>
        <button type="submit" class="btn-main">
          <i class="fa-solid fa-user-plus"></i> Create Account
        </button>
      </form>

      <div class="divider">or</div>
      <p class="link-row">Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>

</body>
</html>