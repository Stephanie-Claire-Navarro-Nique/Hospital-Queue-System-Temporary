<?php
session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['user'])) { 
  header('Location: dashboard.php'); 
  exit; 
}

$errors = []; $email = '';
$usersFile = 'users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if (isset($_COOKIE['remember_email'])) $email = $_COOKIE['remember_email'];

$theme = $_GET['theme'] ?? ($_COOKIE['ui_theme'] ?? 'light');
if (in_array($theme, ['light','dark'])) setcookie('ui_theme', $theme, time()+60*60*24*30, '/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if (empty($errors)) {
        $found = null;
        foreach ($users as $u) { 
          if ($u['email']===$email && password_verify($password,$u['password'])) { 
            $found=$u; 
            break; 
          } 
        }
        if ($found) {
            $_SESSION['user'] = ['id'=>$found['id'],'fname'=>$found['fname'],'email'=>$found['email']];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            setcookie('remember_email', $remember ? $email : '', $remember ? time()+60*60*24*30 : time()-3600, '/');
            header('Location: dashboard.php'); exit;
        } else { $errors[] = "Invalid email or password."; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Login – CareQueue</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="auth-page <?=($theme==='dark'?'dark':'')?>">

  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <header class="site-header auth-header">
    <div class="brand">Care<em>Queue</em></div>
    <div class="header-right">
      <a href="login.php?theme=<?=($theme==='dark'?'light':'dark')?>" class="theme-toggle">
        <i class="fa-solid <?=($theme==='dark'?'fa-sun':'fa-moon')?>"></i>
        <?=($theme==='dark'?'Light':'Dark')?>
      </a>
      <a href="signup.php" class="header-pill">
        <i class="fa-solid fa-user-plus"></i> Sign Up
      </a>
    </div>
  </header>

  <div class="auth-wrap">
    <div class="gradient-bar"></div>
    <div class="auth-card">
      <div class="icon-wrap">
        <i class="fa-solid fa-hospital"></i>
      </div>
      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-sub">Login to the Hospital Queue Staff Portal</p>

      <?php if (!empty($errors)): ?>
      <div class="alert-err">
        <ul><?php foreach($errors as $e):?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul>
      </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="field">
          <label><i class="fa-solid fa-envelope fa-xs"></i> Email Address</label>
          <input type="email" name="email" placeholder="you@hospital.com" value="<?=htmlspecialchars($email)?>"/>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-lock fa-xs"></i> Password</label>
          <input type="password" name="password" placeholder="Your password"/>
        </div>
        <div class="check-row">
          <input type="checkbox" name="remember" id="rem" <?=isset($_COOKIE['remember_email'])?'checked':''?>/>
          <label for="rem">Remember my email</label>
        </div>
        <button type="submit" class="btn-main">
          Login <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
      </form>

      <div class="divider">or</div>
      <p class="link-row">Don't have an account? <a href="signup.php">Sign Up here</a></p>
    </div>
  </div>

</body>
</html>