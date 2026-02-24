<?php
date_default_timezone_set('Asia/Manila');
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$theme = $_GET['theme'] ?? ($_COOKIE['ui_theme'] ?? 'light');
if (in_array($theme, ['light','dark'])) setcookie('ui_theme', $theme, time()+60*60*24*30, '/');

require 'model.php';

$errors  = [];
$success = '';
$name    = '';
$mobile  = '';
$dept    = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name   = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $dept   = trim($_POST['dept']);
    
    if (!preg_match('/^[A-Za-z\s]{3,}$/', $name)) {
        $errors[] = "Name must be letters only and at least 3 characters.";
    }
    
    if (!preg_match('/^09\d{9}$/', $mobile)) {
        $errors[] = "Mobile number must start with 09 and have 11 digits.";
    }

    if (empty($dept)) {
        $errors[] = "Please select a department.";
    }
    
    if (empty($errors)) {
        $newPatient = savePatient($name, $mobile, $dept);
        $success    = $newPatient['queue_no'];
        
        $name   = '';
        $mobile = '';
        $dept   = '';
    }
}

require 'view.php';
?>